// ページコンポーネントをインポートする
import Index from "./pages/Index.vue";
import LogikuraItemCSV from "./pages/logikura/LogikuraItemCSV.vue";
import UpdateStatus from "./pages/notion/UpdateStatus.vue";
import BaseItemCSV from "./pages/baseshop/BaseItemCSV.vue";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: Index,
        meta: {
            title: "新作カード登録",
            description:
                "Wisdom Guildからカード情報を取得して、Notionの商品管理ボードに登録します。",
        },
    },
    {
        path: "/logikura/newitem",
        component: LogikuraItemCSV,
        meta: {
            title: "商品登録CSVダウンロード",
            description:
                "Notionの商品管理ボードからロジクラの商品登録用CSVを作成します。",
        },
    },
    {
        path: "/base/newitem",
        component: BaseItemCSV,
        meta: {
            title: "BASEショップ用CSVダウンロード",
            description:
                "Notionの商品管理ボードからBASEの商品登録用CSVを作成します。",
        },
    },
    {
        path: "/notion/update/status",
        component: UpdateStatus,
        meta: {
            title: "ステータス一括変更",
            description:
                "Notionの商品管理ボードあるカードのステータスを一括で変更します。",
        },
    },
];

// VueRouterインスタンスを作成する
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
