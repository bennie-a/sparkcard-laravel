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
                this.description(c),
                c.price - 63,
                "1",
                c.stock,
                "1",
                showIndex,
                "",
                toSurfaceName(c),
                toNoLabelName(c),
                toRevName(c),
                "",
            ];
            return json;
        },
        description: function (c) {
            let foil = c.isFoil ? "[Foil]" : "";
            let desc = `■商品内容
商品名：「${c.name}${foil}」
エキスパンション：${c.exp.name}(${c.exp.attr})
言語：${c.lang}

■状態
${c.desc}`;
            return desc;
        },
    };
};
