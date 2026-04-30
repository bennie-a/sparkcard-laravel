import { defineStore } from "pinia";
import { ref } from "vue";

// BASE APIの連携状態を管理するストアを定義する。
export const baseConnected = defineStore('baseConnected', () => {
    const isConnected = ref(false);
    const expiresAt = ref(null);

    function connect() {
        isConnected.value = true;
        expiresAt.value = new Date(Date.now() + 60 * 60 * 1000); // 1時間後に期限切れ
    }

    function disconnect() {
        isConnected.value = false;
        expiresAt.value = null;
    }

    function hasConnected() {
        if (!isConnected.value || !expiresAt.value) {
            return false;
        }
        const now = new Date();
        const diff = now.getTime() - expiresAt.value.getTime();
        return isConnected.value = true && Math.abs(diff) / (60 * 1000) < 60; // 1時間以内であれば接続状態を維持
    }

    return {
        isConnected,
        expiresAt,
        connect,
        disconnect,
        hasConnected
    };
});
