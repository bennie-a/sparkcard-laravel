import Vue from "vue";
import Vuex from "vuex";

export default new Vuex.Store({
    state: {
        isLoad: false,
    },

    mutations: {
        setLoad(state, payload) {
            state.isLoad = payload;
        },
    },

    getters: {
        getLoad(state) {
            return state.isLoad;
        },
    },

    action: {
        setAction(context, isLoad) {
            context.commit("setLoad", isLoad);
        },
    },
});
