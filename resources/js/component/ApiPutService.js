import axios from "axios";
import { MsgStore } from "../pages/component/msg/MsgStore";

export const apiPutService = {
    // PUTメソッドでAPIを呼び出す。
    put({url, query, onSuccess, onFinally}) {
        const headers = {
        "Content-Type": "application/json",
        "Accept": "application/json"
    };

    const msgStore = MsgStore();
    msgStore.clear();
    console.log("Updating arrival details:", query);
    axios
            .put( "/api" + url, query, {headers: headers})
            .then((response) => {
                console.log(response.data);
                onSuccess(response.data);
            })
            .catch((e) => {
                let data = e.response.data;
                msgStore.error(data,detail);
            })
            .finally(() => {
                onFinally();
            });
    }

};
