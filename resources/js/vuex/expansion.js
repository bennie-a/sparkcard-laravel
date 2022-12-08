// 検索結果の操作関連クラス
const getDefaultState = () => ({
    list: [],
    suggestions: [],
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        setResult: function (state, list) {
            state.list = list;
        },
        setSuggestions: function (state, suggestions) {
            state.suggestions = suggestions;
        },
        clear: function (state) {
            state.list = [];
            state.suggestions = [];
        },
    },
    getters: {
        result: function (state) {
            return state.list;
        },
        suggestions: function (state) {
            return state.suggestions;
        },
    },
    actions: {
        setResult(context, list) {
            context.commit("setResult", list);
        },
        clear(context) {
            context.commit("clear");
        },
        setSuggestions(context, suggestions) {
            context.commit("setSuggestions", suggestions);
        },
    },
};
