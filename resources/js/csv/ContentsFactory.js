import BaseItemContents from "./BaseItemContents";

const contents = {
    base_item: BaseItemContents(),
};

const ContentsFactory = {
    get: (name) => {
        return contents[name];
    },
};

export default ContentsFactory;
