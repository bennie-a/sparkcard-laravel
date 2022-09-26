import { toItemName } from "../composables/CardCollector";

export default () => {
    return {
        header: ["商品ID", "商品名", "カテゴリID-1", "カテゴリID-2"],
        contents: function (c) {
            let json = [c.baseId, toItemName(c), this.categoryId(c)];
            return json;
        },

        categoryId: function (c) {
            if (c.exp.name !== "団結のドミナリア") {
                return c.exp.baseId;
            }
            const colors = {
                白: "4643612",
                青: "4643614",
                黒: "4643615",
                赤: "4643616",
                緑: "4643618",
                多色: "4643619",
                無色: "4643620",
                アーティファクト: "4643622",
                土地: "4643623",
            };
            return colors[c.color];
        },
    };
};
