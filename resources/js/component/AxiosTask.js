import axios from "axios";
export class AxiosTask {
    constructor($store) {
        this.store = $store;
    }

    // GETメソッドでAPIを呼び出す。
    async get(url, query, success, fail) {
        await axios
            .get(this.getApiUrl(url), { params: { query } })
            .then((response) => {
                success(response, this.store, query);
                this.store.dispatch("setLoad", false);
            })
            .catch((e) => {
                console.error(e);
                fail(e, this.store, query);
                this.$store.dispatch("setLoad", false);
            });
    }

    getApiUrl(url) {
        return "/api" + url;
    }
}
