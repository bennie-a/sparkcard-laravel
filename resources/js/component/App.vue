<script setup>
import TheHeader from "../pages/component/TheHeader.vue";
import SideMenu from "../pages/component/SideMenu.vue";
</script>
<template>
    <TheHeader></TheHeader>
    <div id="contents" class="ui grid padded">
        <nav class="three wide column blue">
            <SideMenu></SideMenu>
        </nav>
        <main id="" class="twelve wide column">
            <h1 class="ui header">
                {{ $route.meta.title }}
                <div class="sub header">
                    {{ $route.meta.description }}
                </div>
            </h1>
            <section class="mt-2">
                <router-view />
            </section>
        </main>
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

#contents {
    background-color: whitesmoke;
}
</style>
<script>
export default {};
</script>
