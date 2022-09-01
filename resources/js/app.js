import "./bootstrap";
import { createApp, ref } from "vue/dist/vue.esm-bundler";
import App from "./component/App.vue";
import "bootstrap/dist/css/bootstrap.min.css";
import router from "./router";

const app = createApp(App);
// ルータをインストール
app.use(router);

app.mount("#app");
