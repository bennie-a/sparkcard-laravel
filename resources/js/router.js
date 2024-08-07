// ページコンポーネントをインポートする
import Index from "./pages/Index.vue";
import UpdateStatus from "./pages/notion/UpdateStatus.vue";
import BaseItemCSV from "./pages/baseshop/BaseItemPage.vue";
import Mercari from "./pages/mercari/MercariItemPage.vue";
import ExpansionPage from "./pages/config/ExpansionPage.vue";
import CardInfoCsvPage from "./pages/config/CardInfoCsvPage.vue";
import CardinfoPage from "./pages/config/CardInfoPage.vue";
import PostExPage from "./pages/config/PostExPage.vue";
import StockpilePage from "./pages/stockpile/StockpilePage.vue";
import ShiptLogPage from "./pages/shipping/ShiptLogPage.vue";
import ShiptLogDssPage from "./pages/shipping/ShiptLogDssPage.vue";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: Index,
        meta: {
            title: "入荷登録",
            description: "DBとNotionの販売管理ボードに在庫カードを登録します。",
        },
    },
    {
        path: "/stockpile/",
        component: StockpilePage,
        meta: {
            title: "在庫情報検索",
            description: "在庫情報を検索します。",
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
        path: "/config/expansion",
        component: ExpansionPage,
        meta: {
            title: "エキスパンション一覧",
            description: "エキスパンション一覧表示を行います。",
        },
    },
    {
        path: "/config/expansion/post",
        component: PostExPage,
        meta: {
            title: "エキスパンション登録",
            description: "エキスパンションの登録・編集を行います。",
            urls:[
                {
                    url:"/config/expansion",
                    title:"エキスパンション一覧"
                },
            ]
        },
        prop: true,
    },
    {
        path: "/config/cardinfo/csv",
        component: CardInfoCsvPage,
        meta: {
            title: "カード情報マスタ一括登録",
            description:
                "MTGJSONからダウンロードしたファイルのカード情報をDBに登録します。",
        },
    },
    {
        path: "/config/cardinfo/post/:setname/:attr",
        name: "PostCardInfo",
        component: CardinfoPage,
        meta: {
            title: "カード情報マスタ登録",
            description: "カード情報をDBに登録します。",
            urls:[
                {
                    url:"/config/expansion",
                    title:"エキスパンション一覧"

                }
            ]
        },
    },
    {
        path: "/shipping/",
        component:ShiptLogPage,
        meta:{
            title:"出荷情報一覧",
            description:"出荷情報を一覧表示します。",
        },
    },
    {
        path:"/shipping/detail/:order_id",
        name:'ShiptLogDss',
        component:ShiptLogDssPage,
        meta:{
            title:"出荷詳細",
            description:"",
            urls: [
                {
                    url:"/shipping/",
                    title:"出荷情報一覧"
                },
            ]
        }
    }
];

// VueRouterインスタンスを作成する
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
