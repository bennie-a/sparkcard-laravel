module.exports = {
    extends: ["plugin:vue/vue3-recommended"],
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
    },
};
