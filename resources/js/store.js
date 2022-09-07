import { createStore } from "vuex";

export const store = createStore({
    state: {
        isLoad: false,
        cards: [],
        paging: {
            currentPage: 1,
            perPage: 10,
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
        setCurrentPage(state, page) {
            state.currentPage = Number(page);
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
        sliceCard: function (state, start) {
            let current = $state.paging.currentPage * $state.paging.perPage;
            // let start = current - $state.paging.perPage;
            return state.cards.slice(start, current);
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
    },
});
