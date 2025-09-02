<script lang="ts">
import {defineComponent} from 'vue'
import {themeStore} from '../store/theme';

export default defineComponent({
    name: "ThemeToggle",
    methods: {
        toggleTheme() {
            themeStore.toggleTheme();
        }
    },
    computed: {
        isDarkMode() {
            return themeStore.state.currentTheme === 'dark';
        }
    }
})
</script>

<template>
    <div class="theme-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="theme-icon sun-icon" :class="{ 'is-active': !isDarkMode }">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
        <label class="switch">
            <input type="checkbox" :checked="isDarkMode" @change="toggleTheme">
            <span class="slider round"></span>
        </label>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="theme-icon moon-icon" :class="{ 'is-active': isDarkMode }">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </div>
</template>

<style scoped>
/* Theme Toggle Styles */
.theme-toggle {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: var(--card-background);
    padding: 0.5rem;
    border-radius: 9999px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
}

.theme-icon {
    color: var(--secondary-color);
    transition: color 0.3s;
}

.theme-icon.is-active {
    color: var(--primary-color);
}

.switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: var(--primary-color);
}

input:checked + .slider:before {
    transform: translateX(14px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>
