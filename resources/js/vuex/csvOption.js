// 検索結果の操作関連クラス
const getDefaultState = () => ({
    selectedList: [],
    isAll: false,
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
        selectList: function (state) {
            return state.seelctList;
        },
        isAll: function (state) {
            return state.isAll;
        },
    },
    actions: {
        setSelected(context, list) {
            context.commit("setSelected", list);
        },
    },
};
