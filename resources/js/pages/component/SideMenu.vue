<template>
    <v-list v-for="n in naviList" :key="n.name">
        <v-list-subheader color="blue-lighten-4" class="text-title-medium"><v-icon :icon="n.icon"></v-icon> {{ n.name }}</v-list-subheader>
        <v-list-item v-for="m in n.menu" :key="m.name" :to="m.link">
            <v-list-item-title><v-icon :icon="m.icon"></v-icon> {{ m.name }}</v-list-item-title>
        </v-list-item>
    </v-list>
    <div class="mt-auto pa-4 text-overline text-medium-emphasis text-center">
        <div class="ui divider"></div>
        <span id="version"><v-icon icon="mdi mdi-tag-outline"></v-icon>ver.{{ version }}</span>
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
#version {
    color: #FAFAFA;
}

.v-list-item-title {
    color: #FAFAFA;
    font-weight: 500;
}

.v-list-item:hover {
    background: #0D47A1;
}

.v-list-item--active {
    background: #0D47A1!important;
}
</style>
