<template>
    <v-list>
        <v-list-subheader>在庫</v-list-subheader>
        <v-list-item title="登録"  class="text-primary" value="add" :to="'/'"></v-list-item>
        <v-list-item title="検索" :to="'/stockpile/'"></v-list-item>
        <v-list-group value="admin">
        </v-list-group>
    </v-list>
    <div id="sidemenu" class="three column row">
        <div class="ui list">
            <div class="item">
                <div class="ui small header">
                    <v-icon icon="mdi-bank"></v-icon>在庫
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/' }"
                            aria-current="page"
                            to="/"
                            ><v-icon icon="mdi-plus-circle-outline"></v-icon>登録</router-link
                        >
                    </li>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/stockpile/' }"
                            aria-current="page"
                            to="/stockpile/"
                            ><v-icon icon="mdi-text-search-variant"></v-icon>検索</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                   <v-icon icon="mdi-book-multiple"></v-icon>入荷
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/arrival' }"
                            aria-current="page"
                            to="/arrival"
                            ><v-icon icon="mdi-text-search-variant"></v-icon>検索</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                   <v-icon icon="mdi-file-download"></v-icon>エクスポート
                </div>
                <ul>
                    <li>
                        <router-link
                            to="/base/newitem"
                            class="nav-link"
                            :class="{ active: $route.path === '/base/newitem' }"
                            ><v-icon icon="mdi-file-delimited-outline"></v-icon>BASE用CSV</router-link
                        >
                    </li>
                    <li>
                        <router-link
                            to="/mercari/newitem"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/mercari/newitem',
                            }"
                            ><v-icon icon="mdi-file-delimited-outline"></v-icon>メルカリ用CSV</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">
                   <v-icon icon="mdi-truck"></v-icon>出荷
                </div>
                <ul>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/mercari/shipt/import' }"
                            aria-current="page"
                            to="/mercari/shipt/import"
                            ><v-icon icon="mdi-database-arrow-up-outline"></v-icon>メルカリ一括登録</router-link>
                    </li>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/base/shipt/import/' }"
                            aria-current="page"
                            to="/base/shipt/import"
                            >BASE一括登録</router-link>
                    </li>
                    <li>
                        <router-link
                            class="nav-link"
                            :class="{ active: $route.path === '/shipping/' }"
                            aria-current="page"
                            to="/shipping/"
                            ><v-icon icon="mdi-text-search-variant"></v-icon>検索</router-link
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
a {
    background: transparent;
}

#sidemenu {
    padding-top: 2rem;
}
.ui.list > .item .header {
    color: #d1ddf3;
    font-weight: 700;
    margin-left: 0.7em;
    font-size: 1.3rem;
}

div.item > ul {
    padding-left: 0.4em;
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
