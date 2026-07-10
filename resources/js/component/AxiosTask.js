import axios from "axios";
import { MsgStore } from "../pages/component/msg/MsgStore";
import { LoadStore } from "../stores/loading/LoadStore";

export class AxiosTask {

    // GETメソッドでAPIを呼び出す。
    async get(url, query, success, fail) {
        await axios
            .get(this.getApiUrl(url), query)
            .then((response) => {
                success(response, query);
            })
            .catch((e) => {
                fail(e, this.store, query);
            })
            .finally(() => {
                // this.store.dispatch("setLoad", false);
            });
    }
    // PATCHメソッドでAPIを呼び出す
    async patch(url, query, success) {
        await axios
            .patch(this.getApiUrl(url), query)
            .then((response) => {
                success(response, query);
                this.store.dispatch(
                    "setSuccessMessage",
                    "更新が完了しました。"
                );
            })
            .catch((e) => {
                console.log(e);
                // this.store.dispatch(["message/error", "更新に失敗しました。"]);
            });
    }
    // POSTメソッドでAPIを呼び出す
    async post(url, json) {
        const msgStore = MsgStore();
        msgStore.clear();

        const loadStore = LoadStore();
        loadStore.on();

        await axios
            .post(this.getApiUrl(url), json)
            .then((response) => {
                if (response.status == 401) {
                    console.log(response.data);
                }
                msgStore.success('登録しました。');
            })
            .catch((e) => {
                if (e.response.status == 422) {
                    console.error(e.response.data);
                    const data = e.response.data;
                    msgStore.error(data.detail);
                    return;
                }
            }).finally(() => {
                loadStore.off();
            });
    }

    getApiUrl(url) {
        return "/api" + url;
    }
}
