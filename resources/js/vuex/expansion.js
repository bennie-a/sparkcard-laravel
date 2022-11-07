// 検索結果の操作関連クラス
const getDefaultState = () => ({
    list: [],
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        setResult: function (state, list) {
            state.list = list;
        },
        clear: function (state) {
            state.list = [];
        },
    },
    getters: {
        result: function (state) {
            return state.list;
        },
    },
    actions: {
        setResult(context, list) {
            context.commit("setResult", list);
        },
        clear(context) {
            context.commit("clear");
        },
    },
};
