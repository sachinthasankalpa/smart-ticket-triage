<template>
    <transition name="toast-fade">
        <div v-if="toast.visible" :class="['toast', `toast--${toast.type}`]">
            <span>{{ toast.message }}</span>
            <button @click="toast.hide()" class="toast__close-button">&times;</button>
        </div>
    </transition>
</template>

<script>
import { defineComponent } from 'vue';
import { toast } from '../store/toast';

export default defineComponent({
    name: 'ToastNotification',
    setup() {
        return { toast };
    }
});
</script>

<style scoped>
.toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    z-index: 9999;
    min-width: 250px;
}

.toast--success {
    background-color: var(--success-color);
}

.toast--error {
    background-color: var(--error-color);
}

.toast__close-button {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    margin-left: 1rem;
    cursor: pointer;
    line-height: 1;
}

.toast-fade-enter-active,
.toast-fade-leave-active {
    transition: opacity 0.5s, transform 0.5s;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
