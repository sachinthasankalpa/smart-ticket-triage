import { reactive } from 'vue';
import apiClient from '../api/axios';
import { toast } from './toast';

export const ticketStore = reactive({
    state: {
        tickets: [],
        ticket: null,
        stats: {},
        pagination: null,
        isLoading: false,
        error: null,
    },

    async fetchTickets(params = { page: 1, search: ''}) {
        this.state.isLoading = true;
        this.state.error = null;
        try {
            const response = await apiClient.get('/tickets', { params });
            this.state.tickets = response.data.data;
            this.state.pagination = {
                meta: response.data.meta,
                links: response.data.links,
            };
        } catch (error) {
            this.state.error = error;
        } finally {
            this.state.isLoading = false;
        }
    },

    async fetchTicket(id) {
        this.state.isLoading = true;
        this.state.error = null;
        this.state.ticket = null;
        try {
            const response = await apiClient.get(`/tickets/${id}`);
            this.state.ticket = response.data;
        } catch (error) {
            this.state.error = error;
        } finally {
            this.state.isLoading = false;
        }
    },

    async createTicket(ticketData) {
        this.state.isLoading = true;
        this.state.error = null;
        try {
            await apiClient.post('/tickets', ticketData);
            toast.show('Ticket created successfully!', 'success');
            // Fetch first page to show the new ticket
            this.fetchTickets();
        } catch (error) {
            this.state.error = error;
        } finally {
            this.state.isLoading = false;
        }
    },

    async updateTicket(id, updateData) {
        this.state.error = null;
        try {
            const response = await apiClient.patch(`/tickets/${id}`, updateData);

            // Update in the list
            const index = this.state.tickets.findIndex(t => t.id === id);
            if (index !== -1) {
                this.state.tickets[index] = response.data;
            }
            // Update in the detail view
            if (this.state.ticket && this.state.ticket.id === id) {
                this.state.ticket = response.data;
            }
            toast.show('Ticket updated successfully.', 'success');
        } catch (error) {
            this.state.error = error;
        }
    },

    async classifyTicket(id) {
        this.state.error = null;
        try {
            await apiClient.post(`/tickets/${id}/classify`);
            toast.show('Classification job started.', 'success');
        } catch (error) {
            this.state.error = error;
        }
    },

    async fetchStats() {
        this.state.isLoading = true;
        this.state.error = null;
        try {
            const response = await apiClient.get('/stats');
            this.state.stats = response.data;
        } catch (error) {
            this.state.error = error;
        } finally {
            this.state.isLoading = false;
        }
    }
});
