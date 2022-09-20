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
                "",
                toItemName(c),
                this.description(c),
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
        description: function (c) {
            let foil = c.isFoil ? "[Foil]" : "";
            let desc = `≪2品以上のお買い上げで割引します♪希望の方は公式Twitter「ベニネコヤ」まで(o^^o)≫
■商品内容
商品名：「${c.name}${foil}」
エキスパンション：${c.exp.name}(${c.exp.attr})
言語：${c.lang}

■状態
■発送について
スリーブに入れた商品をおまけのカードと一緒に透明袋に梱包して【ミニレターorクリックポスト】で発送します。
おまけカードは基本土地カードに変更OKです。購入後の取引メッセージでご希望の色をお伝えください。

≪セット割実施中!!≫
2品以上購入したいなら割引のチャンス!!方法は公式twitter「ベニネコヤ」のDMに欲しい商品名を送るだけ。
後はこちらで用意した専用商品欄から購入すればOK♪

■割引一覧
2品⇒100円引き 3品⇒200円引き 4品以上⇒300円引き

#ベニネコヤ　#TCG #トレーディングカード #mtg #MTGシングル #MTGカード トレカ　マジック・ザ・ギャザリング スタンダード`;
            return desc;
        },
    };
};
