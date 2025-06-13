import axios from "axios";
import {useStore} from 'vuex';

export const apiPutService = {
    // PUTメソッドでAPIを呼び出す。
    put({url, query, onSuccess, onFinally}) {
        const headers = {
        "Content-Type": "application/json",
        "Accept": "application/json"
    };
        axios
            .put( "/api" + url, query, {headers: headers})
            .then((response) => {
                console.log(response.data);
                onSuccess(response.data);
            })
            .catch((e) => {
                let data = e.response.data;
                store.dispatch("message/error", data.detail);
            })
            .finally(() => {
                onFinally();
            });
    }

};
