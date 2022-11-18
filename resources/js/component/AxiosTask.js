import axios from "axios";
export class AxiosTask {
    constructor($store) {
        this.store = $store;
    }

    // GETメソッドでAPIを呼び出す。
    async get(url, query, success, fail) {
        await axios
            .get(this.getApiUrl(url), query)
            .then((response) => {
                success(response, this.store, query);
                this.store.dispatch("setLoad", false);
            })
            .catch((e) => {
                fail(e, this.store, query);
                this.store.dispatch("setLoad", false);
            });
    }
    // PATCHメソッドでAPIを呼び出す
    async patch(url, query, success, fail) {
        await axios
            .patch(this.getApiUrl(url), query)
            .then((response) => {
                success(response, query);
            })
            .catch((e) => {
                fail(e, query);
            });
    }
    // POSETメソッドでAPIを呼び出す
    async post(url, json, success) {
        await axios
            .post(this.getApiUrl(url), json)
            .then((response) => {
                success(response, this.store);
                if (response.status == 401) {
                    console.log(response.data);
                }
            })
            .catch((e) => {
                if (e.response.status == 422) {
                    console.error(e.response.data);
                    return;
                }
            });
    }

    getApiUrl(url) {
        return "/api" + url;
    }
}
