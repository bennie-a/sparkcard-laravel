import { has } from "lodash";
import { defineStore } from "pinia";
import { ref } from "vue";

// メッセージを格納するストアを定義する。
export const piniaMsgStore = defineStore('piniaMsgStore', () =>{
    const success = ref("");
    const error = ref([]);

    // 成功メッセージをセットする。
    function setSuccess(msg) {
        success.value = msg;
    }

    // エラーメッセージをセットする。
    function setError(msg) {
        error.value.push(msg);
    }

    function reset() {
        success.value = "";
        error.value = [];
    }

    function hasSuccess() {
        return success.value !== "";
    }    
    
    function hasError() {
        return error.value.length > 0;
    }

    return {success, error, setSuccess, setError, reset, hasSuccess, hasError};
});