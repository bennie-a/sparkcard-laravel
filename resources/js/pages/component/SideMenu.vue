<template>
    <v-list v-for="n in naviList" :key="n.name">
        <v-list-subheader><v-icon :icon="n.icon"></v-icon> {{ n.name }}</v-list-subheader>
        <v-list-item v-for="m in n.menu" :key="m.name" :to="m.link">
            <v-list-item-title><v-icon :icon="m.icon"></v-icon> {{ m.name }}</v-list-item-title>
        </v-list-item>
    </v-list>
    <div class="mt-auto pa-4 text-overline text-medium-emphasis">
        <div class="ui divider"></div>
        <span><v-icon icon="mdi mdi-tag-outline"></v-icon>{{ version }}</span>
    </div>
</template>
<script setup>
//package.jsonからバージョンを取得。
import { ref } from "vue";
import { version } from "../../../../package";

const naviList = ref([
    {
        name: "在庫",
        icon: "mdi-bank",
        menu:[
            {
                name: "登録",
                link: "/",
                icon: "mdi-plus-circle-outline"
            },
            {
                name: "検索",
                link: "/stockpile/",
                icon: "mdi-text-search-variant"
            }
        ]
    },
    {
        name: "入荷",
        icon: "mdi-book-multiple",
        menu:[
            {
                name: "検索",
                link: "/arrival/",
                icon: "mdi-text-search-variant"
            }
        ]
    },
    {
        name: "エクスポート",
        icon: "mdi-file-download",
        menu:[
            {
                name: "BASE用CSV",
                link: "/base/newitem/",
                icon: "mdi-file-delimited-outline"
            },
            {
                name: "メルカリ用CSV",
                link: "/mercari/newitem/",
                icon: "mdi-file-delimited-outline"
            }
        ]
    },
    {
        name: "注文",
        icon: "mdi-truck",
        menu:[
            {
                name: "インポート",
                link: "/mercari/shipt/import",
                icon: "mdi-database-arrow-up-outline"
            },
            {
                name: "BASE用インポート",
                link: "/base/shipt/import/",
                icon: "mdi-database-arrow-up-outline"
            },
            {
                name: "検索",
                link: "/shipping/",
                icon: "mdi-text-search-variant"
            }
        ]
    },


]);
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
