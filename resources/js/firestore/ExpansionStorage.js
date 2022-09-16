import firebase from "firebase/compat/app";
import "firebase/compat/firestore";

export default class ExpansionStorage {
    constructor() {}

    // IDからコレクションを取得する。
    async findById(id) {
        // Initialize Firebase
        const firebaseConfig = {
            apiKey: import.meta.env.VITE_FIRESTORE_API_KEY,
            authDomain: import.meta.env.VITE_FIRESTORE_DOMAIN,
            projectId: import.meta.env.VITE_FIRESTORE_ID,
            storageBucket: import.meta.env.VITE_FIRESTORE_STORAGE,
            messagingSenderId: import.meta.env.VITE_FIRESTORE_SENDER_ID,
            appId: import.meta.env.VITE_FIRESTORE_APPID,
            measurementId: import.meta.env.VITE_FIRESTORE_MEASUREMENTID,
        };
        firebase.initializeApp(firebaseConfig);
        const db = firebase.firestore();

        const doc = await db.collection("expansion").doc(id).get();
        if (!doc.exists) {
            console.log("No matching documents.");
            return;
        }
        return doc.data();
    }
}
