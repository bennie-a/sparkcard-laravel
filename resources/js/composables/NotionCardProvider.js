import { AxiosTask } from "../component/AxiosTask";
import axios from "axios";

export default class NotionCardProvider {
    // constructor($store) {
    //     this.store = $store;
    // }

    async searchByStatus(query) {
        try {
            console.log("Notion Card Search...");
            const task = new AxiosTask();
            const response = await axios.get("/api/notion/card", query);
            return response.data;
        } catch(e) {
            console.error(e.response.data);
        }finally {

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
