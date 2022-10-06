import {
    toItemName,
    toPhotoName,
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
            "商品画像名_5",
            "商品画像名_6",
            "商品画像名_7",
            "商品画像名_8",
            "商品画像名_9",
            "商品画像名_10",
            "商品名",
            "商品説明",
            "SKU1_種類",
            "SKU1_在庫数",
            "SKU1_商品管理コード",
            "SKU1_JANコード",
            "SKU2_種類",
            "SKU2_在庫数",
            "SKU2_商品管理コード",
            "SKU2_JANコード",
            "SKU3_種類",
            "SKU3_在庫数",
            "SKU3_商品管理コード",
            "SKU3_JANコード",
            "SKU4_種類",
            "SKU4_在庫数",
            "SKU4_商品管理コード",
            "SKU4_JANコード",
            "SKU5_種類",
            "SKU5_在庫数",
            "SKU5_商品管理コード",
            "SKU5_JANコード",
            "SKU6_種類",
            "SKU6_在庫数",
            "SKU6_商品管理コード",
            "SKU6_JANコード",
            "SKU7_種類",
            "SKU7_在庫数",
            "SKU7_商品管理コード",
            "SKU7_JANコード",
            "SKU8_種類",
            "SKU8_在庫数",
            "SKU8_商品管理コード",
            "SKU8_JANコード",
            "SKU9_種類",
            "SKU9_在庫数",
            "SKU9_商品管理コード",
            "SKU9_JANコード",
            "SKU10_種類",
            "SKU10_在庫数",
            "SKU10_商品管理コード",
            "SKU10_JANコード",
            "ブランドID",
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
                this.toPicName(c),
                toNoLabelName(c),
                toRevName(c),
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                toItemName(c),
                this.description(c),
                "",
                c.stock,
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                c.price,
                "ciU8s59qtDYLpdHVJejT8j",
                this.getCondition(c.condition),
                "1",
                "jp27",
                "1",
                "2",
            ];
            return json;
        },
        toPicName: function (c) {
            let name = toPhotoName(c);
            return `${name}_mercari-min.jpg`;
        },
        getCondition(condition) {
            const numbers = {
                NM: "1",
                "NM-": "2",
                "EX+": "3",
                EX: "4",
                PLD: "5",
            };
            let number = numbers[condition];
            if (!number) {
                number = "3";
            }
            return number;
        },
        description: function (c) {
            let foil = c.isFoil ? "[Foil]" : "";
            let delivery = this.getDelivery(c.price);
            let desc = `≪2品以上のお買い上げで割引します♪希望の方は公式Twitter「ベニネコヤ」まで(o^^o)≫
■商品内容
商品名：「${c.name}${foil}」
エキスパンション：${c.exp.name}(${c.exp.attr})
言語：${c.lang}

■状態
${c.desc}
■発送について
スリーブに入れた商品をおまけのカードと一緒に透明袋に梱包して【${delivery}】で発送します。
おまけカードは基本土地カードに変更OKです。購入後の取引メッセージでご希望の色をお伝えください。

≪セット割実施中!!≫
2品以上購入したいなら割引のチャンス!!方法は公式twitter「ベニネコヤ」のDMに欲しい商品名を送るだけ。
後はこちらで用意した専用商品欄から購入すればOK♪

■割引一覧
2品⇒5%引き 3品⇒10%引き 4品以上⇒15%引き

#ベニネコヤ　#TCG #トレーディングカード #mtg #MTGシングル #MTGカード トレカ　マジック・ザ・ギャザリング スタンダード`;
            return desc;
        },

        getDelivery: function (price) {
            let priceNum = Number(price);
            if (priceNum >= 10000) {
                return "簡易書留";
            } else if (priceNum >= 1500 && priceNum < 10000) {
                return "クリックポスト";
            }
            return "ミニレター";
        },
    };
};
