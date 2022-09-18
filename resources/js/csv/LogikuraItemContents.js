import { toItemName } from "../composables/CardCollector";

export default () => {
    return {
        header: [
            "カテゴリ",
            "商品名",
            "種類名",
            "種類画像",
            "販売価格",
            "仕入価格",
            "税率",
        ],
        contents: function (c) {
            let json = [
                c.exp.name,
                toItemName(c),
                "NM",
                c.image,
                c.price,
                "23",
                "10",
            ];
            return json;
        },
    };
};
