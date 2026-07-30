import { AxiosTask } from "../component/AxiosTask";
import axios from "axios";
import { MsgStore } from "../pages/component/msg/MsgStore";

export default class NotionCardProvider {
    // constructor($store) {
    //     this.store = $store;
    // }

    async searchByStatus(query) {
        const msgStore = MsgStore();
        try {
            console.log("Notion Card Search...");
            const task = new AxiosTask();
            const response = await axios.get("/api/notion/card", query);
            console.log(response);
            return response.data;
        } catch(e) {
            msgStore.error(e.response.data.detail);
            throw e;
        }
        // const success = async function (response, query) {
        //     let results = response.data;
        //     console.log("Card Get Count " + results.length);
        //     // store.dispatch("setCard", results);
        // };
        // const fail = function (e, store, query) {
        //     const res = e.response;
        //     console.log(res.data);
        //     store.dispatch("message/error", res.data.detail);
        // };
        // await task.get("/notion/card", query, success, fail);
    }
}
