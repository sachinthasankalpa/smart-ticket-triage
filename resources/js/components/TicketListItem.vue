<template>
    <div class="ticket-item" @click="viewTicket">
        <div class="ticket-item__main">
            <span :class="['status-badge', `status-badge--${ticket.status}`]">{{ ticket.status.replace('_', ' ') }}</span>
            <h3 class="ticket-item__subject">{{ ticket.subject }}</h3>
        </div>
        <div class="ticket-item__meta">
            <span class="ticket-item__category">{{ ticket.category ? ticket.category.replace('_', ' ') : 'N/A' }}</span>
            <span v-if="ticket.internal_note" class="note-badge">Note</span>
            <button @click.stop="classify" class="button button--secondary button--small">Classify</button>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'TicketListItem',
    props: {
        ticket: {
            type: Object,
            required: true,
        },
    },
    emits: ['view', 'classify'],
    methods: {
        viewTicket() {
            this.$emit('view', this.ticket.id);
        },
        classify() {
            this.$emit('classify', this.ticket.id);
        },
    },
});
</script>

<style scoped>
.ticket-item {
    background-color: var(--card-background);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, background-color 0.3s;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ticket-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
}

.ticket-item__subject {
    margin: 0 0 0 1rem;
    font-size: 1.1rem;
}
.ticket-item__main {
    display: flex;
    align-items: center;
}
.ticket-item__meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: var(--secondary-color);
    font-size: 0.9rem;
    text-transform: capitalize;
}

/* Responsive Styles */
@media (max-width: 600px) {
    .ticket-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .ticket-item__meta {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
