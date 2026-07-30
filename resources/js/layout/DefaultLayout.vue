<script setup>
import { ref, onMounted, watch, nextTick, computed } from 'vue'
import { useRoute } from 'vue-router'
import SideMenu from "../pages/component/SideMenu.vue"
import AppBreadcrumb from './AppBreadcrumb.vue'
import { storeToRefs } from "pinia";
import MessageArea from '../pages/component/msg/MessageArea.vue'
import {resolveMetaValue} from './RouteMetaHelper.js';
import Loading from './Loading.vue';
import {LoadStore} from "@/stores/loading/LoadStore.js";

// ----------------------
// route
// ----------------------
const route = useRoute()
const pageTitle = computed(() =>{
    return resolveMetaValue(route.meta.title, route);
});

const loadStore = LoadStore();

</script>
<template>
    <v-layout>
        <v-app-bar>
            <v-app-bar-title>
                <router-link to="/">
                    <span class="mdi mdi-diamond-stone"></span>SPARKCARD
                </router-link>
            </v-app-bar-title>
            <v-toolbar-items>
                <v-btn prepend-icon="mdi-cog" variant="text" :to="'/config/expansion/'">マスタ設定</v-btn>
            </v-toolbar-items>
        </v-app-bar>
        <v-navigation-drawer :width="300" class="bg-blue-darken-3">
            <SideMenu />
        </v-navigation-drawer>
        <v-main class="mt-0 d-flex  justify-start" min-height="100vh">
        <v-container>
            <AppBreadcrumb></AppBreadcrumb>
            <h1 class="text-headline-medium">{{ pageTitle }}</h1>
            <MessageArea></MessageArea>
            <v-sheet class="mt-6 pa-7" rounded>
                <router-view />
            </v-sheet>
            <Loading v-model="loadStore.isLoading" />
        </v-container>
    </v-main>
    </v-layout>
</template>

<style scoped>
.v-container {
  max-width: 1150px !important;
}
</style>
