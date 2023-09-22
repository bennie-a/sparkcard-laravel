module.exports = {
    root: true,
    env: {
        node: true,
    },
    extends: [
        "plugin:vue/vue3-recommended",
        "plugin:vue/essential",
        "eslint:recommended",
    ],
    parserOptions: {
        ecmaVersion: 2020,
    },
    rules: {
        "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
        "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
        "vue/valid-template-root": "off",
        "vue/no-multiple-template-root": "off",
        "vue/html-indent": "off",
        "vue/multi-word-component-names": "off",
        "vue/max-attributes-per-line":"off",
        "no-unused-vars": "off",
        "vue/singleline-html-element-content-newline":"off",
        "vue/html-closing-bracket-spacing":"off",
        "no-undef":"off",
        "vue/attributes-order":"off",
        "vue/html-self-closing":"off",
        "vue/first-attribute-linebreak":"off",
        "vue/html-closing-bracket-newline":"off",
        "vue/attribute-hyphenation":"off"
    },
};
