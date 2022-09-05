<script setup>
import TheHeader from "../pages/component/TheHeader.vue";
import SideMenu from "../pages/component/SideMenu.vue";
</script>
<template>
    <TheHeader></TheHeader>
    <div class="ui grid padded">
        <div class="three wide column blue">
            <SideMenu></SideMenu>
        </div>
        <div class="twelve wide column"><router-view /></div>
    </div>
</template>
<style scoped>
.ui.grid {
    height: 100%;
    min-height: 100%;
}
.ui.grid > .blue.column {
    margin-right: 3em;
    padding-left: 0;
    padding-right: 0;
    background-color: #1e50a2 !important;
    height: 100vh;
}
</style>
