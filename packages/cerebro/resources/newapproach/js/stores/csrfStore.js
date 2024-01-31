import { defineStore } from 'pinia';

export const useCsrfStore = defineStore('csrf', {
    state: () => ({
        token: ''
    }),
    actions: {
        setToken(newToken) {
            this.token = newToken;
        }
    }
});