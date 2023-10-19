import {
    toItemName,
    toSurfaceName,
    toNoLabelName,
    details,
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
            let json = [
                c.baseId,
                toItemName(c),
                "",
                "",
                this.description(c),
                this.price(c.price),
                "1",
                c.stock,
                "1",
                c.index,
                "",
                toSurfaceName(c),
                toNoLabelName(c)
            ];
            return json;
        },
        price(price) {
            if (price < 300) {
                return price;
            }
            let num = price / 10;
            num = Math.round(num - 6.3);
            return num * 10;
        },
        description: function (c) {
            let foil = c.isFoil ? "[Foil]" : "";
            let desc = `■商品内容
商品名：「${c.name}${foil}」
エキスパンション：${c.exp.name}(${c.exp.attr})
言語：${c.lang}
在庫：${c.stock}点

■状態
状態は【${c.condition}】です。${details(c.condition)}
${c.desc}`;
            return desc;
        },
    };
};
