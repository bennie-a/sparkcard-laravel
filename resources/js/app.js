import './bootstrap';
import { createApp } from "vue";
import App from "./component/App.vue";
import Axios from "./component/Axios.vue";

const app = createApp(App);
app.mount("#app");

app.component("axios", Axios);