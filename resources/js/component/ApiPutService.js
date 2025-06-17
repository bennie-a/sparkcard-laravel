import axios from "axios";
import {useStore} from 'vuex';
import { piniaMsgStore } from "@/stores/global/PiniaMsg";

export const apiPutService = {
    // PUTメソッドでAPIを呼び出す。
    put({url, query, onSuccess, onFinally}) {
        const headers = {
        "Content-Type": "application/json",
        "Accept": "application/json"
    };

    console.log("Updating arrival details:", query);
    axios
            .put( "/api" + url, query, {headers: headers})
            .then((response) => {
                console.log(response.data);
                onSuccess(response.data);
            })
            .catch((e) => {
                let data = e.response.data;
                piniaMsgStore().setError(data.detail);
            })
            .finally(() => {
                onFinally();
            });
    }

};
