import './bootstrap';
import { createApp } from "vue";
import App from "./components/App.vue";
import Axios from "./components/Axios";

createApp(App).mount("#app");

const axios = new Vue({
    el: "#axios",
    components: {
        Axios,
    },
});