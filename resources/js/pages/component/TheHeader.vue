<template>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <router-link to="/" class="navbar-brand"
                    ><i class="bi bi-gem"></i> SPARKCARD-Larvel</router-link
                >
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <router-link
                                class="nav-link"
                                :class="{ active: $route.path === '/' }"
                                aria-current="page"
                                to="/"
                                >カード登録</router-link
                            >
                        </li>
                        <li class="nav-item">
                            <router-link
                                to="/about"
                                class="nav-link"
                                :class="{ active: $route.path === '/about' }"
                                >リンク</router-link
                            >
                        </li>
                        <!-- <li class="nav-item">
                            >
                        </li>
                        <li class="nav-item">
                            <router-link
                                class="nav-link"
                                to="/base-cat"
                                :class="{ active: $route.path === '/base-cat' }"
                                >BASE用カテゴリ追加</router-link
                            >
                        </li>
                        <li class="nav-item">
                            <router-link
                                class="nav-link"
                                to="/logikura/item"
                                :class="{
                                    active: $route.path === '/logikura/item',
                                }"
                                >ロジクラ商品データ</router-link
                            >
                        </li>
                        <li class="nav-item">
                            <router-link
                                class="nav-link"
                                to="/logikura/stock"
                                :class="{
                                    active: $route.path === '/logikura/stock',
                                }"
                                >ロジクラ在庫登録</router-link
                            >
                        </li> -->
                    </ul>
                    <!-- <ul class="navbar-nav">
                        <li>
                            <router-link
                                class="nav-link"
                                to="/wisdom/card"
                                :class="{
                                    active: $route.path === '/wisdom/card',
                                }"
                                ><i class="bi bi-file-arrow-down-fill m-1"></i
                                >カード情報取得</router-link
                            >
                        </li>
                    </ul> -->
                </div>
            </div>
        </nav>
    </header>
</template>

<style scoped>
.nav-item {
    margin-right: 2em;
}
.navbar-brand {
    font-size: 1.8em;
    font-family: "Exo 2", sans-serif;
    background-image: linear-gradient(to top right, #1e50a2 0%, #d43f8d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    vertical-align: top;
}
.nav-link {
    font-weight: 500;
    padding-bottom: 2px;
    color: #999 !important;
}
a.navbar-brand i {
    font-size: 0.85em;
}
.nav-link.active {
    font-weight: 500;
    border-bottom: solid 3px #4753a2;
    color: #4753a2 !important;
}
</style>
<script>
export default {
    computed: function navPoint() {
        return this.$route.fullPath;
    },
};
</script>
