<template>
    <div id="logo" class="three column row white">
        <router-link to="/" class="navbar-brand"
            ><i class="bi bi-gem"></i> SPARKCARD</router-link
        >
    </div>
    <div id="sidemenu" class="three column row">
        <div class="ui list">
            <div class="item">
                <div class="ui small header">
                    <i class="bi bi-journal-plus"></i>Notion
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/' }"
                            aria-current="page"
                            to="/"
                            ><i class="bi bi-file-earmark-plus"></i
                            >商品管理ボード登録</router-link
                        >
                    </li>
                    <li>
                        <router-link
                            to="/notion/update/status"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/notion/update/status',
                            }"
                            ><i class="bi bi-kanban"></i
                            >ステータス一括変更</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                    <i class="bi bi-cloud mr-half"></i>ZAIKO
                </div>
                <ul>
                    <li>
                        <router-link
                            to="/zaiko/newitem"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/zaiko/newitem',
                            }"
                            ><i class="bi bi-filetype-csv"></i
                            >商品登録CSV</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                    <i class="bi bi-basket2-fill"></i>販売登録
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
                    <i class="bi bi-gear-fill"></i>設定
                </div>
                <ul>
                    <li>
                        <router-link
                            to="/settings/expansion"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/settings/expansion',
                            }"
                            >エキスパンション一覧</router-link
                        >
                    </li>
                    <li>
                        <router-link
                            to="/settings/cardinfo"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/settings/cardinfo',
                            }"
                            >カード情報マスタ登録</router-link
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
.navbar-brand {
    font-size: 1.8em;
    font-family: "Exo 2", sans-serif;
    background-image: linear-gradient(to top right, #1e50a2 0%, #d43f8d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    vertical-align: top;
}

#sidemenu {
    padding-top: 2rem;
}
.ui.list > .item .header {
    color: white;
    font-weight: 300;
    margin-left: 0.5em;
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
    padding: 1em 1em;
    display: block;
    color: white;
    line-height: 1.6;
}
div.item > ul > li > a:hover {
    background: #2766cc;
}

div.item > ul > li > a.active {
    font-weight: 500;
    background: #2766cc;
    border-left: 5px solid #a333c8;
}

i {
    margin-right: 0.5rem;
}
</style>
