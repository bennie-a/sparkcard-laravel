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
    簡体中国語: (name, enname) => {
        return name + "[CS]";
    },
    イタリア語: (name, enname) => {
        return `${name}[IT] `;
    },
};

// カード名から商品名に変換する。
export const toItemName = (card) => {
    if (card.lang === undefined) {
        return;
    }
    // エキスパンション略称
    let attr = `【${card.exp.attr}】`;
    let foil = card.isFoil ? "【Foil】" : "";
    let name =
        langFuncs[card.lang](card.name, card.enname) + "[" + card.color + "]";
    return attr + foil + name;
};

// カードの表面の画像名に変換する。
export const toSurfaceName = (card) => {
    let name = toPhotoName(card);
    return name + "_base-min.jpg";
};

export const toNoLabelName = (card) => {
    let name = toPhotoName(card);
    return name + "-min.jpg";
};

// カードの裏面の画像名に変換する。
export const toRevName = (card) => {
    let name = toPhotoName(card);
    return name + "裏-min.jpg";
};

export const toPhotoName = (card) => {
    let name = card.enname;
    let filterdname = name.replace(/[<|>|、]/g, "");
    if (card.isFoil) {
        filterdname += "-Foil";
    }
    return filterdname;
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

export const details = (condition) => {
    let templates = {
        NM: "パックから出た直後にスリーブに保管しました。未使用品で初期傷はありません。",
        "NM-": "ダメージが1～2点だけで、ほぼ美品です。",
        "EX+": "よく見るとカードの面や縁に細かいダメージが複数ありますが、ゲームには十分使用できるくらい綺麗な状態です。",
        EX: "一見しただけでカードの面や縁にダメージが複数あります。ゲーム自体には使用できますので、【特価】でご提供いたします。",
    };
    return templates[condition];
};

// const toImageName = (enName) => {
//     let splits = enName.split(" ");
//     splits = splits.map((s) => {
//         let cha = s.replace(",", "").replace("’", "");
//         return cha.toLowerCase();
//     });
//     return splits.join("_");
// };
