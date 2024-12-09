<script setup>
import SideMenu from "../pages/component/SideMenu.vue";
import Loading from "vue-loading-overlay";

</script>
<template>
    <header>
        <div class="ui pointing menu">
            <div class="header navbar-brand">
                <router-link to="/" class="navbar-brand"
                    ><i class="bi bi-gem"></i> SPARKCARD</router-link
                >
            </div>
            <div class="right menu">
                <router-link
                    to="/config/expansion"
                    class="item"
                    :class="{
                        active: $route.path === '/config/expansion',
                    }"
                    >エキスパンション一覧</router-link
                >
                <router-link
                    to="/config/cardinfo/csv"
                    class="item"
                    :class="{
                        active: $route.path === '/config/cardinfo/csv',
                    }"
                    >カード情報マスタ登録</router-link
                >
            </div>
        </div>
    </header>
    <div id="contents" class="ui grid padded">
        <nav
            ref="sidebar"
            class="three wide column blue"
            :style="{ height: higherHeightPx }"
        >
            <SideMenu></SideMenu>
        </nav>
        <main id="main" ref="main" class="twelve wide column">
            <nav v-if="$route.meta.urls" class="ui breadcrumb">
                <span v-for="url in $route.meta.urls" :key="url">
                    <router-link :to="url.url" class="section">{{ url.title }}</router-link>
                    <i class="right angle icon divider"></i>
                </span>
                <span class="active section">{{ $route.meta.title }}</span>
            </nav>
            <h1 class="ui header">
                {{ $route.meta.title }}
                <div class="sub header">
                    {{ $route.meta.description }}
                </div>
            </h1>
            <section class="mt-2 ui segment">
                <router-view />
            </section>
            <loading
            :active="$store.dispatch['loading/isLoad']" :can-cancel="false" :is-full-page="true" />
        </main>
    </div>
</template>
<script>
export default {
    data() {
        return {
            initHeight: 0,
            sidebarHeight: 0,
            mainHeight: 0,
            isMounted: false,
            urls:[]
        };
    },
    components:{
        Loading
    },
    computed: {
        higherHeightPx() {
            return this.isMounted
                ? Math.max(this.sidebarHeight, this.mainHeight) + "px"
                : null;
        },
        isLoading() {
            return this.$store.dispatch('loading/isLoad');
        }
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
    mounted: function () {
        this.$store.dispatch("clearCards");
        this.$store.dispatch("clearMessage");

        // メイン部分のリサイズ検知
        const main = document.querySelector("#main");
        const resizeObserver = new ResizeObserver((entries) => {
            this.mainHeight = this.$refs.main.clientHeight;
        });
        resizeObserver.observe(main);
        this.sidebarHeight = this.$refs.sidebar.clientHeight;
        this.mainHeight = this.$refs.main.clientHeight;
        this.initHeight = this.mainHeight;
        this.isMounted = true;
    },
};
</script>
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
