export default () => {
    return {
        header: ["バーコード", "在庫数"],
        contents: function (c) {
            let json = [c.barcode, c.stock];
            return json;
        },
    };
};
