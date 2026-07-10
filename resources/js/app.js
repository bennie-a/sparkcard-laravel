import { createApp } from "vue/dist/vue.esm-bundler";
import App from "./component/App.vue";
import router from "./router";
import VuePapaParse from "vue-papa-parse";
import { store } from "./store.js";
// import Encoding from "encoding-japanese";
import "vue-loading-overlay/dist/css/index.css";
import {createPinia} from 'pinia';
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css' // おそらくssrを使うとき必要
import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { VDateInput } from 'vuetify/labs/VDateInput'
import * as yup from 'yup';
import { requiredMessage } from '../validation/messages.js';
import { ja } from 'vuetify/locale';
const app = createApp(App);

// ルータをインストール
app.use(router);
app.use(store);
app.use(VuePapaParse);
// app.use(Encoding);
const vuetify = createVuetify({
  components:{
    ...components,
    VDateInput
  },
  directives,
    theme: {
      defaultTheme: 'light'
    },
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  defaults: {
        VTextField: {
            variant: 'outlined',
            density: 'compact',
            bgColor: 'white',
        },
        VBtn:{
            variant: 'flat',
            rounded: 'xs',
        },
        VSelect: {
            variant: 'outlined',
            density: 'compact',
            bgColor: 'white',
        },
        VDateInput:{
            density:'compact',
            locale: {
                ja: 'ja-JP',
            },
            variant:'outlined',
            prependIcon:"",
        }
    },
    locale: {
        locale: 'ja',
        messages: { ja },
  },
});
app.use(vuetify);

yup.setLocale({
    mixed:{
        required:requiredMessage
    }
});

const pinia = createPinia();
app.use(pinia);

app.mount("#app");
