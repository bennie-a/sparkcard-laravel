<script setup>
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

// ページ遷移の度に祖先ページのタイトルとリンクを取得する。
const breadcrumbs = computed(() => {
    const items = [];
    let currentRoute = route;
    while(currentRoute) {
        items.unshift({
            title: currentRoute.meta.title,
            to: currentRoute.path
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
        <v-breadcrumbs class="pl-0 mb-1" v-if="breadcrumbs.length > 1">
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
                />
            </template>
            <v-breadcrumbs-item>
                {{ route.meta.title }}
            </v-breadcrumbs-item>
        </v-breadcrumbs>
</template>
