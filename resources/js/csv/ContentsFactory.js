import BaseCatContents from "./BaseCatContents";
import BaseItemContents from "./BaseItemContents";
import MercariContents from "./MercariContents";

const contents = {
    base_item: BaseItemContents(),
    mercari_item: MercariContents(),
    base_category: BaseCatContents(),
};

const ContentsFactory = {
    get: (name) => {
        return contents[name];
    },
};

export default ContentsFactory;
