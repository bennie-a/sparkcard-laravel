import {ref} from 'vue';
import {defineStore} from 'pinia';
export const LoadStore = defineStore('LoadStore',  {
    state:() => ({
        isLoading: ref(false)
    }),
    actions: {
        on() {
            this.isLoading = true;
        },
        off() {
            this.isLoading = false;
        }
    }
})
