// ロジクラの商品登録用CSVファイルをダウンロードクラス
import { toItemName } from "../composables/CardCollector";

export default () => {
    return {
        header: ["物品名", "カテゴリ", "数量", "単位", "保管場所", "備考"],
        contents: function (c) {
            let json = [
                toItemName(c),
                c.exp.name,
                c.stock,
                "枚",
                "通常版BOX",
                "",
            ];
            return json;
        },
    };
};
