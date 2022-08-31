import './bootstrap';
import { createApp, ref } from "vue/dist/vue.esm-bundler";
import App from "./component/App.vue";
import Axios from "./component/Axios.vue";
import "bootstrap/dist/css/bootstrap.min.css";

createApp(App).mount("#app");