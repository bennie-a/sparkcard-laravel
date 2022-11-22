import { AxiosTask } from "../component/AxiosTask";

export default class NotionCardProvider {
    constructor($store) {
        this.store = $store;
    }

    async searchByStatus(query, filtered) {
        console.log("Notion Card Search...");
        this.store.dispatch("setLoad", true);
        this.store.dispatch("clearCards");
        const task = new AxiosTask(this.store);
        const status = this.store.getters["search/status"];
        console.log(status);
        const success = async function (response, store, query) {
            console.log(response.status);
            let results = response.data;
            let cards = results.filter((r) => filtered(r));
            console.log("Card Get Count " + cards.length);
            store.dispatch("setCard", cards);
            store.dispatch(
                "setSuccessMessage",
                cards.length + "件取得しました。"
            );
        };
        const fail = function (e, store, query) {
            const res = e.response;
            console.error(e);
            console.log(res.data);
            store.dispatch("message/error", res.data.message);
        };
        await task.get("/notion/card?status=" + status, query, success, fail);
    }
}
