// メッセージ管理クラス
const getDefaultState = () => ({
    success: "",
    error: "",
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        error(state, error) {
            state.error = error;
        },
        clear(state) {
            state = getDefaultState();
        },
    },
    getters: {
        error: function (state) {
            return state.error;
        },
    },
    actions: {
        error(context, error) {
            context.commit("error", error);
        },
        clear(context) {
            context.commit("clear");
        },
    },
};
