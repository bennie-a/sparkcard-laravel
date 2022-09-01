<template>
    <h1>Hello World Laravel9+Vue</h1>
    <span>テンプレート</span>
    <button class="btn btn-primary">ボタン</button>
    <router-view />

    <router-link class="nav-link" aria-current="page" to="/">Index</router-link>

    <router-link class="nav-link" aria-current="page" to="/about"
        >About</router-link
    >
</template>
