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
    computed: {
        isLoad() {
            return this.store.state.isLoad;
        },
    },
    mutations: {
        setLoad(state, isLoad) {
            state.isLoad = isLoad;
        },

        pushCard(state, card) {
            state.cards.push(card);
        },
        setCurrentPage(state, page) {
            state.currentPage = Number(page);
        },
    },

    getters: {
        cardsLength: function (state) {
            return state.cards.length;
        },
        card: function () {
            return state.cards;
        },
        sliceCard: function (state, start) {
            let current = $state.paging.currentPage * $state.paging.perPage;
            let start = current - $state.paging.perPage;
            return state.cards.slice(start, current);
        },
    },

    actions: {
        setLoad(context, isLoad) {
            context.commit("setLoad", isLoad);
        },

        pushCard(context, card) {
            context.commit("pushCard", card);
        },
        setCurrentPage(context, page) {
            context.commit("setCurrentPage", page);
        },
    },
});
