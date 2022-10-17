<script setup>
import SideMenu from "../pages/component/SideMenu.vue";
</script>
<template>
    <div id="contents" class="ui grid padded">
        <nav
            id="sidebar"
            ref="sidebar"
            class="three wide column blue"
            :style="{ height: higherHeightPx }"
        >
            <SideMenu></SideMenu>
        </nav>
        <main id="main" ref="main" class="twelve wide column">
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
.ui.grid .blue.column {
    margin-right: 3em;
    padding-left: 0;
    padding-right: 0;
    background-color: #1e50a2 !important;
    height: 100vh;
    padding-top: 0;
}

#contents {
    background-color: whitesmoke;
}
</style>
<script>
export default {
    data() {
        return {
            initHeight: 0,
            sidebarHeight: 0,
            mainHeight: 0,
            isMounted: false,
        };
    },
    mounted: function () {
        this.$store.dispatch("clearCards");
        this.$store.dispatch("clearMessage");

        // メイン部分のリサイズ検知
        const main = document.querySelector("#main");
        const resizeObserver = new ResizeObserver((entries) => {
            this.mainHeight = this.$refs.main.clientHeight;
            console.log(`メインの高さ ${this.mainHeight}`);
        });
        resizeObserver.observe(main);
        this.sidebarHeight = this.$refs.sidebar.clientHeight;
        this.mainHeight = this.$refs.main.clientHeight;
        this.initHeight = this.mainHeight;
        this.isMounted = true;
    },
    computed: {
        higherHeightPx() {
            return this.isMounted
                ? Math.max(this.sidebarHeight, this.mainHeight) + "px"
                : null;
        },
    },
    watch: {
        $route(to, from) {
            this.$store.dispatch("clearCards");
            this.$store.dispatch("clearMessage");
            this.$store.dispatch("message/clear");
            // 画面が替わる度に高さをリセット。
            this.mainHeight = this.initHeight;
        },
    },
};
</script>
