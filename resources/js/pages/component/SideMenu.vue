<template>
    <div id="sidemenu" class="three column row">
        <div class="ui list">
            <div class="item">
                <div class="ui small header">
                    在庫
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/' }"
                            aria-current="page"
                            to="/"
                            ><i class="bi bi-plus-circle-fill"></i>登録</router-link
                        >
                    </li>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/stockpile/' }"
                            aria-current="page"
                            to="/stockpile/"
                            ><i class="bi bi-search"></i>検索</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                   入荷
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/arrival' }"
                            aria-current="page"
                            to="/arrival"
                            ><i class="bi bi-search"></i>検索</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                   エクスポート
                </div>
                <ul>
                    <li>
                        <router-link
                            to="/base/newitem"
                            class="nav-link"
                            :class="{ active: $route.path === '/base/newitem' }"
                            ><i class="bi bi-filetype-csv"></i
                            >BASE用CSV</router-link
                        >
                    </li>
                    <li>
                        <router-link
                            to="/mercari/newitem"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/mercari/newitem',
                            }"
                            ><i class="bi bi-filetype-csv"></i
                            >メルカリ用CSV</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                   出荷
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/shipping/import' }"
                            aria-current="page"
                            to="/shipping/import"
                            ><i class="bi bi-upload"></i>一括登録</router-link>
                    </li>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/shipping/' }"
                            aria-current="page"
                            to="/shipping/"
                            ><i class="bi bi-search"></i>検索</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="ui divider"></div>
            <span>ver.{{ version }}</span>
        </div>
    </div>
</template>
<script setup>
//package.jsonからバージョンを取得。
import { version } from "../../../../package";
</script>
<style scoped>
#logo {
    background: white;
    padding: 1rem 0.5rem;
}
#sidemenu {
    padding-top: 2rem;
}
.ui.list > .item .header {
    color: #d1ddf3;
    font-weight: 700;
    margin-left: 0.7em;
    font-size: 1.2rem;
}

div.item > ul {
    padding-left: 0;
    margin-top: 0.5em;
}
div.item > ul > li {
    list-style: none;
}

span {
    text-align: center;
    display: block;
}

div.item > ul > li > a {
    padding: 0.8em 1em;
    display: block;
    color: white;
    line-height: 1.4;
    font-size: 1.1rem;
    font-weight: 500;
}
div.item > ul > li > a:hover {
    background: #2766cc;
}

div.item > ul > li > a.active {
    font-weight: 500;
    background: #2766cc;
    border-right: 5px solid #cc5df1;
}

i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}
</style>
