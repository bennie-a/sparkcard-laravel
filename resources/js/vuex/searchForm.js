// 検索フォームのクラス
const getDefaultState = () => ({
    status: "要撮影",
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        status(state, status) {
            state.status = status;
        },
    },
    getters: {
        status: function (state) {
            return state.status;
        },
    },
    actions: {
        status(context, status) {
            context.commit("status", status);
        },
    },
};
