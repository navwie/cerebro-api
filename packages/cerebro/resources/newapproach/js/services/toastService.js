import { reactive, computed } from "vue";

const toasts = reactive([]);

export function useToast() {
    const addToast = (message, type = "info", duration = 5000) => {
        const toastId = Symbol();
        toasts.push({ id: toastId, message, type });

        setTimeout(() => {
            const index = toasts.findIndex((t) => t.id === toastId);
            if (index !== -1) {
                toasts.splice(index, 1);
            }
        }, duration);
    };

    const removeToast = (toastId) => {
        const index = toasts.findIndex((t) => t.id === toastId);
        if (index !== -1) {
            toasts.splice(index, 1);
        }
    };

    return { toasts: computed(() => toasts), addToast, removeToast };
}
