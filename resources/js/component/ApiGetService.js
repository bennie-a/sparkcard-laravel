import axios from "axios";
import {useStore} from 'vuex';

export const apiService = {
    // GETメソッドでAPIを呼び出す。
    get({url, query, onSuccess, onFinally}) {
        const store = useStore();
        axios
            .get( "/api" + url, query)
            .then((response) => {
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
