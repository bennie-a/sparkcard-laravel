let langFuncs = {
    日本語: (name, enname) => {
        return name + "[JP]";
    },
    英語: (name, enname) => {
        return name + "/" + enname + "[EN]";
    },
    繁体中国語: (name, enname) => {
        return name + "[CT]";
    },
};

// カード名から商品名に変換する。
export const toItemName = (card) => {
    if (card.lang === undefined) {
        return;
    }
    // エキスパンション略称
    let attr = "【DMU】";
    let foil = card.isFoil ? "【Foil】" : "";
    let name =
        langFuncs[card.lang](card.name, card.enName) + "[" + card.color + "]";
    return attr + foil + name;
};

// カードの表面の画像名に変換する。
export const toSurfaceName = (name) => {
    return name + "-min.jpg";
};

// カードの裏面の画像名に変換する。
export const toRevName = (name) => {
    return name + "-rev-min.jpg";
};

export const createTemplate = (card) => {
    let template =
        "■商品内容\n商品名：「<商品名>」\n" +
        "エキスパンション：団結のドミナリア(DMU)\n" +
        "言語：日本語\n\n■状態\n" +
        "状態は【NM】です。パック開封直後にスリーブに入れました。";
    template = template.replace("<商品名>", card.name);
    return template;
};

const toImageName = (enName) => {
    let splits = enName.split(" ");
    splits = splits.map((s) => {
        let cha = s.replace(",", "").replace("’", "");
        return cha.toLowerCase();
    });
    return splits.join("_");
};
