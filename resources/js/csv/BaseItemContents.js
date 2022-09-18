import {
    toItemName,
    toSurfaceName,
    toRevName,
    toNoLabelName,
} from "../composables/CardCollector";

export default () => {
    return {
        header: [
            "商品ID",
            "商品名",
            "種類ID",
            "種類名",
            "説明",
            "価格",
            "税率",
            "在庫数",
            "公開状態",
            "表示順",
            "種類在庫数",
            "画像1",
            "画像2",
            "画像3",
            "画像4",
        ],
        contents: function (c) {
            let showIndex = c.exp.orderId * 10 + c.index;
            let json = [
                c.baseId,
                toItemName(c),
                "",
                "",
                "",
                c.price,
                "1",
                c.stock,
                this.isPublic ? 1 : 0,
                showIndex,
                "",
                toSurfaceName(c),
                toNoLabelName(c),
                toRevName(c),
                "",
            ];
            return json;
        },
    };
};
