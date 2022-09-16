import { createStore } from "vuex";
import search from "./vuex/searchForm";
import message from "./vuex/messageForm";
import csvOption from "./vuex/csvOption";

export const store = createStore({
    modules: { search, message, csvOption },
    state: {
        isLoad: false,
        cards: [],
        paging: {
            currentPage: 1,
            perPage: 10,
        },
        msg: {
            success: "",
            error: "",
        },
        form: {
            itemStatus: [
                "ロジクラ要登録",
                "販売保留",
                "要撮影",
                "BASE登録予定",
            ],
        },
        table: {
            selectedCard: [],
        },
    },
    mutations: {
        setLoad(state, isLoad) {
            state.isLoad = isLoad;
        },

        pushCard(state, card) {
            state.cards.push(card);
        },
        setCard(state, cards) {
            state.cards = cards;
        },
        clearCards(state) {
            state.cards.splice(0);
        },
        setCurrentPage(state, page) {
            state.paging.currentPage = Number(page);
        },
        setSuccessMessage(state, msg) {
            state.msg.success = msg;
        },
        error(state, msg) {
            state.msg.error = msg;
        },
        clearMessage(state) {
            state.msg.success = "";
            state.msg.error = "";
        },
    },

    getters: {
        isLoad: function (state) {
            return state.isLoad;
        },
        cardsLength: function (state) {
            return state.cards.length;
        },
        card: function (state) {
            return state.cards;
        },
        sliceCard: function (state) {
            let current = state.paging.currentPage * state.paging.perPage;
            let start = current - state.paging.perPage;
            return state.cards.slice(start, current);
        },
        getCurrentPage: function (state) {
            return state.paging.currentPage;
        },
        getItemStatus: function (state) {
            return state.form.itemStatus;
        },
    },

    actions: {
        // 読み込み中フラグ
        setLoad(context, isLoad) {
            context.commit("setLoad", isLoad);
        },
        // カード一覧
        pushCard(context, card) {
            context.commit("pushCard", card);
        },
        setCard(context, cards) {
            context.commit("setCard", cards);
        },
        // 表示中のページ番号
        setCurrentPage(context, page) {
            context.commit("setCurrentPage", page);
        },
        clearCards: function (context) {
            context.commit("clearCards");
        },
        setSuccessMessage: function (context, msg) {
            context.commit("setSuccessMessage", msg);
        },
        clearMessage: function (context) {
            context.commit("clearMessage");
        },
    },
});
