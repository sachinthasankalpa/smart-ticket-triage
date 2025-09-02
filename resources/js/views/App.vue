<template>
    <div class="app-container">
        <header class="app-header">
            <h1 class="app-header__title">Smart Ticket Triage</h1>
            <nav class="app-header__nav">
                <router-link to="/tickets" class="app-header__nav-link">Tickets</router-link>
                <router-link to="/dashboard" class="app-header__nav-link">Dashboard</router-link>
            </nav>
        </header>
        <main class="app-main">
            <router-view></router-view>
        </main>
        <ThemeToggle/>
        <ToastNotification />
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import ToastNotification from '../components/ToastNotification.vue';
import { themeStore } from '../store/theme';
import ThemeToggle from "../components/ThemeToggle.vue";

export default defineComponent({
    name: 'App',
    components: {
        ThemeToggle,
        ToastNotification,
    },
    created() {
        themeStore.initTheme();
    },
});
</script>

<style scoped>
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.app-header {
    background-color: var(--primary-color);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow);
}

.app-header__title {
    margin: 0;
    font-size: 1.5rem;
}

.app-header__nav-link {
    color: white;
    text-decoration: none;
    margin-left: 1.5rem;
    font-weight: 600;
    transition: opacity 0.2s;
    padding-bottom: 0.25rem;
    border-bottom: 2px solid transparent;
}

.app-header__nav-link:hover {
    opacity: 0.8;
}

.app-header__nav-link.router-link-exact-active {
    opacity: 1;
    border-bottom-color: white;
}

.app-main {
    flex-grow: 1;
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}
</style>
