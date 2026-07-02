// ページコンポーネントをインポートする
import Index from "./pages/Index.vue";
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
const arrivalDssLinks = {url:"/arrival/date/", title:"入荷情報詳細"};
const routes = [
    {
        path: "/",
        component: Index,
        meta: {
            layout: 'default',
            title: "在庫登録",
        },
    },
    {
        path:arrivalLinks.url,
        component:ArrivalLogPage,
        name:'ArrivalLog',
        meta:{
            title:arrivalLinks.title,
            breadscrumb:arrivalLinks.title,
        },
    },
    {
        path:arrivalDssLinks.url,
        name:'ArrivalLogDss',
        component:ArrivalLogDssPage,
        meta:{
            parent:'ArrivalLog',
            title:(route) => {
                const arrDateStore = arrDateConditionStore();
                return `入荷情報:${arrDateStore.arrivalDate}`;
            },
            breadscrumb:(route) => {
                const arrDateStore = arrDateConditionStore();
                console.log('arrivalDssLinks.title', arrDateStore.arrivalDate);
                return arrDateStore.arrivalDate;
            },
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
            parent:'ArrivalLogDss',
            title:(route) => {
                return `入荷情報編集(No.${route.params.arrival_id})`;
            },
            breadscrumb:(route) => {
                return `No.${route.params.arrival_id}`;
            },
        },

    },
    {
        path: "/stockpile/",
        component: StockpilePage,
        meta: {
            title: "在庫情報検索",
        },
    },
    {
        path: "/mercari/newitem",
        component: Mercari,
        meta: {
            title: "商品登録用CSVダウンロード",
        },
    },
    {
        path: "/config/expansion",
        name:'Ex',
        component: ExpansionPage,
        meta: {
            title: "エキスパンション一覧",
            breadscrumb: "エキスパンション一覧"
        },
    },
    {
        path: "/config/expansion/post",
        name:'ExPost',
        component: PostExPage,
        meta: {
            title: "エキスパンション登録",
            parent:'Ex',
            breadscrumb:"エキスパンション登録",
        },
    },
    {
        path: "/config/cardinfo/csv/",
        name:"CardInfoCsvPage",
        component: CardInfoBulkPage,
        meta: {
            title: "カード情報一括登録",
            parent:'Ex',
            breadscrumb:"カード情報一括登録"
        },
    },
    {
        path: "/config/cardinfo/post",
        name: "PostCardInfo",
        component: CardinfoPage,
        meta: {
            title: "カード情報登録",
            parent:'Ex',
            breadscrumb:"カード情報登録",
        },
    },
    {
        path: "/shipping/",
        component:ShiptLogPage,
        name:'Shipt',
        meta:{
            title:"注文情報一覧",
            breadscrumb:"注文情報一覧",
        },
    },
    {
        path:"/shipping/detail/:order_id",
        name:'ShiptLogDss',
        component:ShiptLogDssPage,
        meta:{
            parent:'Shipt',
            breadscrumb:(route) => {
                return route.params.order_id;
            },
            title:(route) => {
                return `注文情報:${route.params.order_id}`;
            },
        },
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
