<script setup>
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import {resolveMetaValue} from './RouteMetaHelper.js';

const route = useRoute();
const router = useRouter();

// ページ遷移の度に祖先ページのタイトルとリンクを取得する。
const breadcrumbs = computed(() => {
    const items = [];
    let currentRoute = route;
    while(currentRoute) {
        console.log(currentRoute.path, typeof currentRoute.meta.breadscrumb);
        let title = resolveMetaValue(currentRoute.meta.breadscrumb, currentRoute);

        items.unshift({
            title: title,
            to: {path:currentRoute.path, query:route.query}
        });

        const parentName = currentRoute.meta.parent;
        if (!parentName) {
            break;
        }

        const parentRoute = router
            .getRoutes()
            .find(r => r.name === parentName);
        if (!parentRoute) {
            break;
        }

        currentRoute = {path:parentRoute.path, meta:parentRoute.meta};
    }
    return items;
});

</script>
<template>
        <v-breadcrumbs class="pt-0 pl-0 pb-1" v-if="breadcrumbs.length > 1">
            <template
                v-for="r, in breadcrumbs"
                :key="r.title"
            >
                <v-breadcrumbs-item v-if="r.title !== route.meta.title">
                    <v-btn variant="plain" color="teal-lighten-1" :to="r.to" class="pa-0">{{ r.title }}</v-btn>
                </v-breadcrumbs-item>
                <v-icon
                icon="mdi-chevron-right"
                size="small"
                 v-if="r.title !== route.meta.title"
                  color="grey-darken-2"
                />
            </template>
            <v-breadcrumbs-item color="grey-darken-2">
                {{ route.meta.title }}
            </v-breadcrumbs-item>
        </v-breadcrumbs>
</template>
