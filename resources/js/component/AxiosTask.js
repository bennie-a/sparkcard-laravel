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
    async store(url, json, success) {
        await axios
            .post(this.getApiUrl(url), json)
            .then((response) => {
                if (response.status == 201) {
                    success(response);
                }
            })
            .catch((e) => {});
    }

    getApiUrl(url) {
        return "/api" + url;
    }
}
