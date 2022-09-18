import firebase from "firebase/compat/app";
import "firebase/compat/firestore";
import db from "./connection";

export default class ExpansionStorage {
    constructor() {}

    // IDからコレクションを取得する。
    async findById(id) {
        const doc = await db.collection("expansion").doc(id).get();
        if (!doc.exists) {
            console.log("No matching documents." + id);
            return;
        }
        return doc.data();
    }
}
