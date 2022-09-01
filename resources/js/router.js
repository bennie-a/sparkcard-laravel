// ページコンポーネントをインポートする
import Index from "./pages/Index.vue";
import About from "./pages/About.vue";

import { createRouter, createWebHistory } from "vue-router";

const routes = [
    { path: "/", component: Index },
    { path: "/about", component: About },
];

// VueRouterインスタンスを作成する
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
