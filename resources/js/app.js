import { createApp, ref } from "vue/dist/vue.esm-bundler";
import App from "./component/App.vue";
import router from "./router";
import "semantic-ui-css/semantic.min.css";
import "semantic-ui-css//semantic.min.js";
import Paginate from "vuejs-paginate-next";
const app = createApp(App);
import { store } from "./store.js";

// ルータをインストール
app.use(router);
app.use(Paginate);
app.use(store);
app.mount("#app");
