import { createStore } from "vuex";

export const store = createStore({
    state: {
        isLoad: false,
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
    },

    getters: {},

    actions: {
        setLoad(context, isLoad) {
            context.commit("setLoad", isLoad);
        },
    },
});
