<template>
    <div class="ticket-details-container">
        <router-link to="/tickets" class="back-link">&larr; Back to all tickets</router-link>
        <div v-if="isLoading" class="loader">Loading...</div>
        <div v-else-if="ticket" class="ticket-details">
            <header class="header">
                <h2>{{ ticket.subject }}</h2>
                <span :class="['status-badge', `status-badge--${editableTicket.status}`]">{{ editableTicket.status.replace('_', ' ') }}</span>
            </header>

            <div class="content-grid">
                <div class="main-content">
                    <div class="body">
                        <p>{{ ticket.body }}</p>
                    </div>
                    <div class="note">
                        <h4>Internal Note</h4>
                        <textarea v-model="editableTicket.internal_note" placeholder="Add an internal note..." class="form-textarea"></textarea>
                    </div>
                </div>

                <div class="sidebar">
                    <div class="details-card">
                        <h4>Details</h4>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" v-model="editableTicket.status" class="form-select">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" v-model="editableTicket.category" class="form-select">
                                <option value="technical_support">Technical Support</option>
                                <option value="billing">Billing</option>
                                <option value="sales">Sales</option>
                                <option value="general_inquiry">General Inquiry</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>AI Confidence</label>
                            <p>{{ ticket.ai_confidence !== null ? `${(ticket.ai_confidence * 100).toFixed(0)}%` : 'N/A' }}</p>
                        </div>

                        <div class="form-group">
                            <label>AI Explanation</label>
                            <p>{{ ticket.ai_explanation || 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="actions">
                        <button @click="runClassification" class="button button--secondary" :disabled="isClassifying">
                            {{ isClassifying ? 'Running...' : 'Run Classification' }}
                        </button>
                        <button @click="handleUpdate" class="button button--primary" :disabled="isSaving || !isChanged">
                            {{ isSaving ? 'Saving...' : 'Update Ticket' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="empty-state">Ticket not found.</div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { ticketStore } from '../store/tickets';

export default defineComponent({
    name: 'TicketDetails',
    props: {
        id: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isSaving: false,
            isClassifying: false,
            editableTicket: {
                status: '',
                category: null,
                internal_note: ''
            },
        };
    },
    computed: {
        ticket() {
            return ticketStore.state.ticket;
        },
        isLoading() {
            return ticketStore.state.isLoading;
        },
        isChanged() {
            if (!this.ticket) return false;
            return (
                this.editableTicket.status !== this.ticket.status ||
                this.editableTicket.category !== (this.ticket.category ? this.ticket.category : null) ||
                this.editableTicket.internal_note !== this.ticket.internal_note
            );
        }
    },
    watch: {
        ticket: {
            handler(newTicket) {
                if (newTicket) {
                    this.setEditableData(newTicket);
                }
            },
            immediate: true,
            deep: true,
        }
    },
    methods: {
        setEditableData(ticket) {
            this.editableTicket.status = ticket.status;
            this.editableTicket.category = ticket.category ? ticket.category : null;
            this.editableTicket.internal_note = ticket.internal_note;
        },
        async handleUpdate() {
            if (!this.isChanged) return;

            const payload = {};
            if (this.editableTicket.status !== this.ticket.status) {
                payload.status = this.editableTicket.status;
            }
            if (this.editableTicket.category !== (this.ticket.category ? this.ticket.category : null)) {
                payload.category = this.editableTicket.category;
            }
            if (this.editableTicket.internal_note !== this.ticket.internal_note) {
                payload.internal_note = this.editableTicket.internal_note;
            }

            this.isSaving = true;
            await ticketStore.updateTicket(this.id, payload);
            this.isSaving = false;
        },
        async runClassification() {
            this.isClassifying = true;
            await ticketStore.classifyTicket(this.id);
            // After classifying, we should refresh the ticket data to get the new explanation/confidence
            await ticketStore.fetchTicket(this.id);
            this.isClassifying = false;
        }
    },
    created() {
        ticketStore.fetchTicket(this.id);
    },
});
</script>

<style scoped>
.ticket-details-container {
    max-width: 1100px;
    margin: auto;
}
.back-link {
    display: inline-block;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}
.header h2 {
    margin: 0;
}
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}
.main-content, .sidebar {
    background: var(--card-background);
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: var(--shadow);
    transition: background-color 0.3s;
}
.body {
    margin-bottom: 2rem;
}
.details-card {
    background: none;
    padding: 0;
    box-shadow: none;
}
.details-card h4 {
    margin-top: 0;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}
.details-card .form-group {
    margin-bottom: 1rem;
}
.details-card .form-group p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--secondary-color);
}
.note h4 {
    margin-top: 0;
    margin-bottom: 1rem;
}
.actions {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

@media (max-width: 1100px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}
</style>
