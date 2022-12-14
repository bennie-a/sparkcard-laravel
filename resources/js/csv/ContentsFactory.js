import BaseCatContents from "./BaseCatContents";
import BaseItemContents from "./BaseItemContents";
import LogikuraItemContents from "./LogikuraItemContents";
import LogikuraStockContents from "./LogikuraStockContents";
import MercariContents from "./MercariContents";

const contents = {
    base_item: BaseItemContents(),
    logikura_item: LogikuraItemContents(),
    logikura_stock: LogikuraStockContents(),
    mercari_item: MercariContents(),
    base_category: BaseCatContents(),
};

const ContentsFactory = {
    get: (name) => {
        return contents[name];
    },
};

export default ContentsFactory;
