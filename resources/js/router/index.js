import { createRouter, createWebHistory } from 'vue-router';
import TicketList from '../components/TicketList.vue';
import TicketDetails from '../components/TicketDetails.vue';
import Dashboard from '../components/Dashboard.vue';

const routes = [
    {
        path: '/',
        redirect: '/tickets',
    },
    {
        path: '/tickets',
        name: 'tickets',
        component: TicketList,
    },
    {
        path: '/tickets/:id',
        name: 'ticket-details',
        component: TicketDetails,
        props: true,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
