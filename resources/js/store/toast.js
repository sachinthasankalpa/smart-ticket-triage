import { reactive } from 'vue';

export const toast = reactive({
    visible: false,
    message: '',
    type: 'success', // 'success' or 'error'
    timeoutId: null,

    show(message, type = 'success', duration = 3000) {
        this.message = message;
        this.type = type;
        this.visible = true;

        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        this.timeoutId = setTimeout(() => {
            this.hide();
        }, duration);
    },

    hide() {
        this.visible = false;
        this.message = '';
        this.type = 'success';
        clearTimeout(this.timeoutId);
        this.timeoutId = null;
    }
});
