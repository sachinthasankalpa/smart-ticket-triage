<template>
    <div class="ticket-list-container">
        <header class="header">
            <h2>All Tickets</h2>
            <div class="header-actions">
                <button @click="exportToCSV" class="button button--secondary">Export to CSV</button>
                <button @click="isModalOpen = true" class="button button--primary">New Ticket</button>
            </div>
        </header>

        <div class="filters">
            <input type="text" v-model="searchQuery" placeholder="Search subject or body..." class="form-input"/>
        </div>

        <div v-if="isLoading" class="loader">Loading...</div>
        <div v-else-if="tickets.length > 0">
            <div class="ticket-list">
                <TicketListItem
                    v-for="ticket in tickets"
                    :key="ticket.id"
                    :ticket="ticket"
                    @view="viewTicket"
                    @classify="classify"
                />
            </div>
            <div class="pagination-controls">
                <button @click="prevPage" :disabled="!canGoPrev" class="button button--secondary">Previous</button>
                <span>Page {{ paginationMeta.current_page }} of {{ paginationMeta.last_page }}</span>
                <button @click="nextPage" :disabled="!canGoNext" class="button button--secondary">Next</button>
            </div>
        </div>
        <div v-else class="empty-state">No tickets found.</div>

        <NewTicketModal :isOpen="isModalOpen" @close="isModalOpen = false" />

    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { ticketStore } from '../store/tickets';
import { toast } from '../store/toast';
import NewTicketModal from './NewTicketModal.vue';
import TicketListItem from './TicketListItem.vue';

export default defineComponent({
    name: 'TicketList',
    components: { NewTicketModal, TicketListItem },
    data() {
        return {
            isModalOpen: false,
            searchQuery: '',
            debouncedFetch: null,
        };
    },
    computed: {
        tickets() {
            return ticketStore.state.tickets;
        },
        isLoading() {
            return ticketStore.state.isLoading;
        },
        paginationMeta() {
            return ticketStore.state.pagination?.meta || {};
        },
        canGoPrev() {
            return this.paginationMeta.current_page > 1;
        },
        canGoNext() {
            return this.paginationMeta.current_page < this.paginationMeta.last_page;
        }
    },
    watch: {
        searchQuery() {
            this.debouncedFetch();
        }
    },
    methods: {
        debounce(func, delay) {
            let timeoutId;
            return (...args) => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        },
        fetchData(page = 1) {
            ticketStore.fetchTickets({ page, search: this.searchQuery });
        },
        nextPage() {
            if (this.canGoNext) {
                this.fetchData(this.paginationMeta.current_page + 1);
            }
        },
        prevPage() {
            if (this.canGoPrev) {
                this.fetchData(this.paginationMeta.current_page - 1);
            }
        },
        viewTicket(id) {
            this.$router.push({ name: 'ticket-details', params: { id } });
        },
        classify(id) {
            ticketStore.classifyTicket(id);
        },
        exportToCSV() {
            if (this.tickets.length === 0) {
                toast.show('No tickets to export.', 'error');
                return;
            }

            const headers = [
                'ID', 'Subject', 'Body', 'Status', 'Category',
                'Created At', 'Updated At', 'Internal Note'
            ];

            const escapeCSV = (str) => {
                if (str === null || str === undefined) return '';
                const newStr = String(str);
                if (newStr.includes(',') || newStr.includes('"') || newStr.includes('\n')) {
                    return `"${newStr.replace(/"/g, '""')}"`;
                }
                return newStr;
            };

            const rows = this.tickets.map(ticket => [
                ticket.id,
                escapeCSV(ticket.subject),
                escapeCSV(ticket.body),
                ticket.status.value,
                ticket.category ? ticket.category.value : '',
                ticket.created_at,
                ticket.updated_at,
                escapeCSV(ticket.internal_note)
            ].join(','));

            const csvContent = [headers.join(',')].concat(rows).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", `tickets-${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    },
    created() {
        this.debouncedFetch = this.debounce(() => this.fetchData(1), 500);
        this.fetchData();
    }
});
</script>

<style scoped>
.ticket-list-container {
    max-width: 1100px;
    margin: auto;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.filters {
    margin-bottom: 1.5rem;
}

.pagination-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 2rem;
    gap: 1rem;
}
</style>
