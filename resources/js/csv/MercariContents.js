import {
    toItemName,
    toSurfaceName,
    toRevName,
    toNoLabelName,
} from "../composables/CardCollector";

export default () => {
    return {
        header: [
            "商品画像名_1",
            "商品画像名_2",
            "商品画像名_3",
            "商品画像名_4",
            "商品名",
            "商品説明",
            "SKU1_在庫数",
            "販売価格",
            "カテゴリID",
            "商品の状態",
            "配送方法",
            "発送元の地域",
            "発送までの日数",
            "商品ステータス",
        ],
        contents: function (c) {
            let json = [
                toSurfaceName(c),
                toNoLabelName(c),
                toRevName(c),
                toItemName(c),
                "",
                "",
                c.stock,
                c.price,
                "ciU8s59qtDYLpdHVJejT8j",
                "1",
                "1",
                "jp27",
                "1",
                "1",
            ];
            return json;
        },
    };
};
