import { reactive } from 'vue';

export const themeStore = reactive({
    state: {
        currentTheme: 'light',
    },

    initTheme() {
        const savedTheme = localStorage.getItem('theme');
        const userPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme) {
            this.state.currentTheme = savedTheme;
        } else if (userPrefersDark) {
            this.state.currentTheme = 'dark';
        } else {
            this.state.currentTheme = 'light';
        }
        this.applyTheme();
    },

    toggleTheme() {
        this.state.currentTheme = this.state.currentTheme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.state.currentTheme);
        this.applyTheme();
    },

    applyTheme() {
        document.documentElement.setAttribute('data-theme', this.state.currentTheme);
    }
});
