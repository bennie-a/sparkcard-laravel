// メッセージ管理クラス
const getDefaultState = () => ({
    success: "",
    error: "",
    errorhtml: "",
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        error(state, error) {
            state.error = error;
        },
        success(state, success) {
            state.success = success;
        },
        errorhtml(state, error) {
            state.errorhtml = error;
        },
        clear(state) {
            state.error = "";
            state.success = "";
            state.errorhtml = "";
        },
    },
    getters: {
        error: function (state) {
            return state.error;
        },
        errorhtml: function (state) {
            return state.errorhtml;
        },
    },
    actions: {
        error(context, error) {
            context.commit("error", error);
        },
        errorhtml(context, error) {
            context.commit("errorhtml", error);
        },
        clear(context) {
            context.commit("clear");
        },
    },
};
