// ページコンポーネントをインポートする
import Index from "./pages/Index.vue";
import LogikuraItemCSV from "./pages/zaiko/LogikuraItemPage.vue";
import UpdateStatus from "./pages/notion/UpdateStatus.vue";
import BaseItemCSV from "./pages/baseshop/BaseItemPage.vue";
import Mercari from "./pages/mercari/MercariItemPage.vue";
import ExpansionPage from "./pages/settings/ExpansionPage.vue";
import CardInfoPage from "./pages/settings/CardInfoPage.vue";
import PostExPage from "./pages/settings/PostExPage.vue";
import Packing from "./pages/packing/Packing.vue";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: Index,
        meta: {
            title: "カード登録",
            description: "販売カードをNotionの商品管理ボードに登録します。",
        },
    },
    {
        path: "/zaiko/newitem",
        component: LogikuraItemCSV,
        meta: {
            title: "在庫登録CSVダウンロード",
            description:
                "Notionの商品管理ボードからZAIKOの商品登録用CSVを作成します。",
        },
    },
    {
        path: "/base/newitem",
        component: BaseItemCSV,
        meta: {
            title: "BASEショップ用CSVダウンロード",
            description: "Notionの商品管理ボードからBASE用CSVを作成します。",
        },
    },
    {
        path: "/mercari/newitem",
        component: Mercari,
        meta: {
            title: "メルカリ用CSVダウンロード",
            description:
                "Notionの商品管理ボードからメルカリ用CSVを作成します。※300円未満の商品は除外します。",
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
    {
        path: "/settings/expansion",
        component: ExpansionPage,
        meta: {
            title: "エキスパンション一覧",
            description: "エキスパンション一覧表示を行います。",
        },
    },
    {
        path: "/settings/expansion/post",
        component: PostExPage,
        meta: {
            title: "エキスパンション登録",
            description: "エキスパンションの登録・編集を行います。",
        },
    },
    {
        path: "/settings/cardinfo",
        component: CardInfoPage,
        meta: {
            title: "カード情報マスタ登録",
            description:
                "MTGJSONからダウンロードしたファイルのカード情報をDBに登録します。",
        },
    },
    {
        path: "/packing/",
        component: Packing,
        meta: {
            title: "宛名ラベル作成",
            description: "注文情報CSVファイルから宛名ラベルを作成します。",
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
