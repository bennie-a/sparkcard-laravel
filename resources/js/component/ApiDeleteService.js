import axios from "axios";
import {useStore} from 'vuex';

export const apiDeleteService = {
    // API経由で削除する。
    delete({url, id, onSuccess, onFinally}) {
        axios
        .delete( "/api" + url + id)
        .then((response) => {
                onSuccess(response);
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
