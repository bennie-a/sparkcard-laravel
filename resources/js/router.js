// ページコンポーネントをインポートする
import Index from "./pages/Index.vue";
import BaseItemCSV from "./pages/baseshop/BaseItemPage.vue";
import Mercari from "./pages/mercari/MercariItemPage.vue";
import ExpansionPage from "./pages/config/ExpansionPage.vue";
import CardinfoPage from "./pages/config/CardInfoPage.vue";
import PostExPage from "./pages/config/PostExPage.vue";
import StockpilePage from "./pages/stockpile/StockpilePage.vue";
import ShiptLogPage from "./pages/shipping/ShiptLogPage.vue";
import ShiptLogDssPage from "./pages/shipping/ShiptLogDssPage.vue";
import ShiptLogImpPage from "./pages/mercari/ShiptMercariImpPage.vue";
import ArrivalLogPage from "./pages/arrival/ArrivalLogPage.vue";
import { createRouter, createWebHistory } from "vue-router";
import{ store} from './store';
import ArrivalLogDssPage from "./pages/arrival/ArrivalLogDssPage.vue";
import ArrivalLogEditPage from "./pages/arrival/ArrivalLogEditPage.vue";
import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";
import CardInfoBulkPage from "./pages/config/CardInfoBulkPage.vue";
import BaseApiIntegration from "./pages/baseshop/BaseApiIntegration.vue";
import ShiptLogBaseImpPage from "./pages/baseshop/ShiptBaseImpPage.vue";
import { authGuard } from "./auth/auth-guard";

const arrivalLinks = {url:"/arrival/",    title:"入荷情報一覧"};
const arrivalDssLinks = {url:"/arrival/date/", title:""};
const routes = [
    {
        path: "/",
        component: Index,
        meta: {
            layout: 'default',
            title: "在庫登録",
            description: "DBとNotionの販売管理ボードに在庫カードを登録します。",
        },
    },
    {
        path:arrivalLinks.url,
        component:ArrivalLogPage,
        meta:{
            title:arrivalLinks.title,
            description:"入荷情報を一覧表示します"
        },
    },
    {
        path:arrivalDssLinks.url,
        name:'ArrivalLogDss',
        component:ArrivalLogDssPage,
        beforeEnter:(to, from, next) => {
            const arrDateStore = arrDateConditionStore();
            arrivalDssLinks.title = arrDateStore.arrivalDate;
            to.meta.title = arrDateStore.arrivalDate;
            next();
        },
        meta:{
            urls: [arrivalLinks]
        },
    },
    {
        path:"/arrival/edit/:arrival_id",
        name:'ArrivalLogEdit',
        component:ArrivalLogEditPage,
        beforeEnter:(to, from, next) => {
            to.meta.title = 'No.' + to.params.arrival_id;
            next();
        },
        meta:{
            urls: [arrivalLinks, arrivalDssLinks]
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
        path: "/config/expansion",
        name:'Ex',
        component: ExpansionPage,
        meta: {
            title: "エキスパンション一覧",
        },
    },
    {
        path: "/config/expansion/post",
        name:'ExPost',
        component: PostExPage,
        meta: {
            title: "エキスパンション登録",
            parent:'Ex',
        },
    },
    {
        path: "/config/cardinfo/csv/",
        name:"CardInfoCsvPage",
        component: CardInfoBulkPage,
        meta: {
            title: "カード情報一括登録",
            parent:'Ex',
        },
    },
    {
        path: "/config/cardinfo/post",
        name: "PostCardInfo",
        component: CardinfoPage,
        meta: {
            title: "カード情報登録",
            parent:'Ex',
        },
    },
    {
        path: "/shipping/",
        component:ShiptLogPage,
        name:'Shipt',
        meta:{
            title:"注文情報一覧",
        },
    },
    {
        path:"/shipping/detail/:order_id",
        name:'ShiptLogDss',
        component:ShiptLogDssPage,
        beforeEnter:(to, from, next) => {
            to.meta.title = to.params.order_id;
            next();
        },
        meta:{
            parent:'Shipt'
        }
    },
    {
        path: "/mercari/shipt/import",
        component:ShiptLogImpPage,
        meta:{
            title:"メルカリ注文情報一括登録",
            description:"メルカリShopsから出荷情報をCSVファイルで一括登録します。",
        }
    },
    {
        path: "/base/auth/",
        component:BaseApiIntegration,
        meta:{
            layout:'blank',
        },
    },
    {
        path: "/base/shipt/import/",
        component:ShiptLogBaseImpPage,
        meta:{
            title:"BASE注文情報一括登録",
            description:"BASEショップの注文情報を一括登録します。",
            requiresBase:true
        }
    },

];

// VueRouterインスタンスを作成する
const router = createRouter({
    history: createWebHistory(),
    routes,
});

authGuard(router);
// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
