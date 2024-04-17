import { AxiosTask } from "../component/AxiosTask";

export default class NotionCardProvider {
    constructor($store) {
        this.store = $store;
    }

    async searchByStatus(query) {
        console.log("Notion Card Search...");
        this.store.dispatch("setLoad", true);
        this.store.dispatch("clearCards");
        const task = new AxiosTask(this.store);

        const success = async function (response, store, query) {
            console.log(response.status);
            let results = response.data;
            console.log("Card Get Count " + results.length);
            store.dispatch("setCard", results);
        };
        const fail = function (e, store, query) {
            const res = e.response;
            console.log(res.data);
            store.dispatch("message/error", res.data.detail);
        };
        await task.get("/notion/card", query, success, fail);
    }
}
