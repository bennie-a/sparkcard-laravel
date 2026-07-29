import axios from "axios";
import { MsgStore } from "../pages/component/msg/MsgStore";

export const apiService = {
    // GETメソッドでAPIを呼び出す。
    get({url, query, onSuccess, onFinally}) {
        const msgStore = MsgStore();
        msgStore.clear();
        axios
            .get( "/api" + url, query)
            .then((response) => {
                onSuccess(response.data);
            })
            .catch((e) => {
                let data = e.response.data;
                msgStore.error(data.detail);
            })
            .finally(() => {
                onFinally();
            });
    }

};
