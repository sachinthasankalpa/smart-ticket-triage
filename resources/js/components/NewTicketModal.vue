<template>
    <div v-if="isOpen" class="modal-overlay" @click.self="close">
        <div class="modal-content">
            <header class="modal-header">
                <h3>New Support Ticket</h3>
                <button @click="close" class="close-button">&times;</button>
            </header>
            <div class="modal-body">
                <form @submit.prevent="submitTicket" novalidate>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input
                            type="text"
                            id="subject"
                            v-model="newTicket.subject"
                            class="form-input"
                            :class="{ 'input-error': errors.subject }"
                            @blur="validateField('subject')"
                            required
                        >
                        <p v-if="errors.subject" class="error-message">{{ errors.subject }}</p>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <textarea
                            id="body"
                            v-model="newTicket.body"
                            class="form-textarea"
                            :class="{ 'input-error': errors.body }"
                            @blur="validateField('body')"
                            rows="5"
                            required
                        ></textarea>
                        <p v-if="errors.body" class="error-message">{{ errors.body }}</p>
                    </div>
                    <div class="form-actions">
                        <button type="button" @click="close" class="button button--secondary">Cancel</button>
                        <button
                            type="submit"
                            class="button button--primary"
                            :disabled="isLoading || isFormInvalid"
                        >
                            {{ isLoading ? 'Submitting...' : 'Submit Ticket' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { ticketStore } from '../store/tickets';

export default defineComponent({
    name: 'NewTicketModal',
    props: {
        isOpen: Boolean,
    },
    emits: ['close'],
    data() {
        return {
            newTicket: {
                subject: '',
                body: '',
            },
            errors: {
                subject: '',
                body: ''
            }
        };
    },
    computed: {
        isLoading() {
            return ticketStore.state.isLoading;
        },
        isFormInvalid() {
            return !(this.newTicket.subject && this.newTicket.body);
        }
    },
    methods: {
        close() {
            this.$emit('close');
        },
        validateField(field) {
            if (!this.newTicket[field]) {
                this.errors[field] = `${field} is required.`;
            } else {
                this.errors[field] = '';
            }
        },
        async submitTicket() {
            this.validateField('subject');
            this.validateField('body');

            if (this.isFormInvalid) {
                return;
            }

            await ticketStore.createTicket(this.newTicket);
            if (!ticketStore.state.error) {
                this.newTicket.subject = '';
                this.newTicket.body = '';
                this.errors.subject = '';
                this.errors.body = '';
                this.close();
            }
        },
    },
});
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
}

.modal-content {
    background-color: var(--card-background);
    padding: 2rem;
    border-radius: 0.5rem;
    width: 90%;
    max-width: 500px;
    transition: background-color 0.3s;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    transition: border-color 0.3s;
}
.modal-header h3 {
    margin: 0;
}
.close-button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-color);
}
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
}

.input-error {
    border-color: var(--error-color);
}
.input-error:focus {
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--error-color) 20%, transparent);
}
.error-message {
    color: var(--error-color);
    font-size: 0.875rem;
    margin-top: 0.25rem;
    margin-bottom: 0;
}
</style>
