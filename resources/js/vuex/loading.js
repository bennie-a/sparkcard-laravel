// 「Waiting...」のstore管理クラス
const getDefaultState = () => ({
    isLoad: false,
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        setLoad(state, isLoad) {
            state.isLoad = isLoad;
        },
    },
    getters: {
        isLoad: function (state) {
            return state.isLoad;
        },
    },
    actions: {
        start(context) {
            context.commit("setLoad", true);
        },
        stop(context) {
            context.commit("setLoad", false);
        },
    },
};
