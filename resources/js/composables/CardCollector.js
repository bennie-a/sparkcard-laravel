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
export const toSurfaceName = (enName) => {
    return toImageName(enName) + "-min.jpg";
};

// カードの裏面の画像名に変換する。
export const toRevName = (enName) => {
    return toImageName(enName) + "-rev-min.jpg";
};

const toImageName = (enName) => {
    let splits = enName.split(" ");
    splits = splits.map((s) => {
        let cha = s.replace(",", "").replace("’", "");
        return cha.toLowerCase();
    });
    return splits.join("_");
};
