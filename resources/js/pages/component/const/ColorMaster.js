// 色に関する定義クラス
export class ColorMaster {
  // 1. 外部から変更できないように凍結して定義
  static get MAP() {
    return Object.freeze({
        W: Object.freeze({state:'白', color: "yellow-darken-1", icon: 'mdi-weather-sunny' }),
        U: Object.freeze({ state:'青',  color: "blue-darken-2", icon: 'mdi-water-outline' }),
        B: Object.freeze({ state:'黒', color: "grey-darken-3", icon: 'mdi-skull-outline' }),
        R: Object.freeze({ state:'赤', color: "red-darken-2", icon: 'mdi-fire' }),
        G: Object.freeze({ state:'緑', color: "green-darken-2", icon: 'mdi-pine-tree-variant-outline' }),
        M: Object.freeze({ state:'多色', color: "orange-darken-1", icon: 'mdi-multiplication-box' }),
        L: Object.freeze({ state:'無色', color: "grey-lighten-1", icon: 'mdi-rhombus-outline' }),
        A: Object.freeze({ state:'アーティファクト', color: "blue-grey-lighten-1", icon: 'mdi-gold' }),
        Land: Object.freeze({ state:'土地', color: "brown-lighten-1", icon: 'mdi-map-marker-radius-outline' }),
        Art: Object.freeze({ state:'アートカード', color: "pink-lighten-1", icon: 'mdi-palette' }),
        T:Object.freeze({state:'トークン', color:'indigo-lighten-1', icon:"mdi-chess-pawn"}),
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
    }));-lighten-3
  }
}
