// 検索結果の操作関連クラス
const getDefaultState = () => ({
    selectedList: [],
});

export default {
    namespaced: true,
    state: getDefaultState(),
    mutations: {
        setSelected: function (state, list) {
            state.selectedList = list;
        },
    },
    getters: {
        selectedList: function (state) {
            return state.selectedList;
        },
    },
    actions: {
        setSelected(context, list) {
            context.commit("setSelected", list);
        },
    },
};
