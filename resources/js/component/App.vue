<script setup>
import TheHeader from "../pages/component/TheHeader.vue";
</script>
<template>
    <TheHeader></TheHeader>
    <router-view />
    <router-link class="nav-link" aria-current="page" to="/">Index</router-link>

    <router-link class="nav-link" aria-current="page" to="/about"
        >About</router-link
    >
</template>
