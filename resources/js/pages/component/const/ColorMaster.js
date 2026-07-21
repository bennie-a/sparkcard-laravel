// 色に関する定義クラス
export class ColorMaster {
  // 1. 外部から変更できないように凍結して定義
  static get MAP() {
    return Object.freeze({
        W: Object.freeze({state:'白', color: "yellow", icon: 'mdi-weather-sunny' }),
        U: Object.freeze({ state:'青',  color: "blue", icon: 'mdi-water-outline' }),
        B: Object.freeze({ state:'黒', color: "black", icon: 'mdi-skull-outline' }),
        R: Object.freeze({ state:'赤', color: "red", icon: 'mdi-fire' }),
        G: Object.freeze({ state:'緑', color: "green", icon: 'mdi-pine-tree-variant-outline' }),
        M: Object.freeze({ state:'多色', color: "orange", icon: 'mdi-multiplication-box' }),
        L: Object.freeze({ state:'無色', color: "grey", icon: 'mdi-rhombus-outline' }),
        A: Object.freeze({ state:'アーティファクト', color: "blue-grey", icon: 'mdi-gold' }),
        Land: Object.freeze({ state:'土地', color: "brown", icon: 'mdi-map-marker-radius-outline' }),
        Art: Object.freeze({ state:'アートカード', color: "pink", icon: 'mdi-palette' }),
        T:Object.freeze({state:'トークン', color:'indigo', icon:"mdi-chess-pawn"}),
    });
  }

  // 2. キー（W, Bなど）から安全にデータを取得するメソッド
  static find(key) {
    return this.MAP[key] || { color: 'grey', icon: 'mdi-help-circle-outline' };
  }

  // 3. v-forループで回しやすいように配列（List）として取得するゲッター
  static get list() {
    return Object.entries(this.MAP).map(([key, value]) => ({
      key,
      ...value
    }));
  }
}
