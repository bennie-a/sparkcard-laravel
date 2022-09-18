import {
    toItemName,
    toSurfaceName,
    toRevName,
    toNoLabelName,
} from "../composables/CardCollector";

export default () => {
    return {
        base_item: function (c) {
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
