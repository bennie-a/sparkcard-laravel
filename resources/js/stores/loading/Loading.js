import {ref} from 'vue';
import {defineStore} from 'pinia';
export const useLoadingStore = defineStore('useLoadingStore', () => {
    const isLoading = ref(false);

    // Loading画面を表示する。
    function start() {
        isLoading.value = true;
    }

    function stop() {
        isLoading.value = false;
    }
    return {isLoading, start, stop}
}
