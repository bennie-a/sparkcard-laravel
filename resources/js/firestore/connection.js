// Import the functions you need from the SDKs you need
import firebase from "firebase/compat/app";
import "firebase/compat/firestore";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIRESTORE_API_KEY,
    authDomain: import.meta.env.VITE_FIRESTORE_DOMAIN,
    projectId: import.meta.env.VITE_FIRESTORE_ID,
    storageBucket: import.meta.env.VITE_FIRESTORE_STORAGE,
    messagingSenderId: import.meta.env.VITE_FIRESTORE_SENDER_ID,
    appId: import.meta.env.VITE_FIRESTORE_APPID,
    measurementId: import.meta.env.VITE_FIRESTORE_MEASUREMENTID,
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const db = firebase.firestore();
export default db;
