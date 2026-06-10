<script setup>
import { computed } from 'vue';
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
{{ breadcrumbs }}
</template>
