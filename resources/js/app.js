import { createApp, ref } from "vue/dist/vue.esm-bundler";
import App from "./component/App.vue";
import router from "./router";
import "semantic-ui-css/semantic.min.css";
import "semantic-ui-css/semantic.min.js";
import Paginate from "vuejs-paginate-next";
import VuePapaParse from "vue-papa-parse";
const app = createApp(App);
import { store } from "./store.js";
import Encoding from "encoding-japanese";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Draggable from "vuedraggable";
import vfmPlugin from "vue-final-modal";

// ルータをインストール
app.use(router);
app.use(Paginate);
app.use(store);
app.use(VuePapaParse);
app.use(Encoding);
app.use(Datepicker);
app.use(Draggable);
app.use(vfmPlugin);
app.mount("#app");
