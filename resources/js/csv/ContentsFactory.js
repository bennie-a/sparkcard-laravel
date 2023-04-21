import BaseCatContents from "./BaseCatContents";
import BaseItemContents from "./BaseItemContents";
import LogikuraItemContents from "./LogikuraItemContents";
import MercariContents from "./MercariContents";

const contents = {
    base_item: BaseItemContents(),
    zaiko_item: LogikuraItemContents(),
    mercari_item: MercariContents(),
    base_category: BaseCatContents(),
};

const ContentsFactory = {
    get: (name) => {
        return contents[name];
    },
};

export default ContentsFactory;
