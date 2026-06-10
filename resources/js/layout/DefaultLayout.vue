<script setup>
import { ref, onMounted, watch, nextTick, computed } from 'vue'
import { useRoute } from 'vue-router'
import SideMenu from "../pages/component/SideMenu.vue"
import Loading from "vue-loading-overlay"
import "vue-loading-overlay/dist/css/index.css"
import AppBreadcrumb from './AppBreadcrumb.vue'
import { LoadingStore } from '@/stores/loading/Loading'
import { storeToRefs } from "pinia";
import MessageArea from '../pages/component/msg/MessageArea.vue'

// ----------------------
// store
// ----------------------
const loadingStore = LoadingStore()
const { isLoading } = storeToRefs(loadingStore)

// ----------------------
// route
// ----------------------
const route = useRoute()

// ----------------------
// DOM refs
// ----------------------
const sidebar = ref(null)
const main = ref(null)
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
            <v-breadcrumbs v-if="route.meta.breads" class="pl-0 mb-1">
                <template
                    v-for="(title, index) in route.meta.breads"
                    :key="index"
                >
                    <v-breadcrumbs-item color="grey-darken-1">
                    {{ title }}
                    </v-breadcrumbs-item>

                    <v-icon
                    v-if="index < route.meta.breads.length"
                    icon="mdi-chevron-right"
                    size="small"
                     color="grey-darken-1"
                    />
                </template>
                <v-breadcrumbs-item color="grey-darken-1">
                    {{ route.meta.title }}
                </v-breadcrumbs-item>
            </v-breadcrumbs>
            <AppBreadcrumb></AppBreadcrumb>
            <h1 class="text-headline-medium">{{ route.meta.title }}</h1>
            <MessageArea></MessageArea>
            <v-sheet class="mt-6 pa-7" rounded>
                <router-view />
            </v-sheet>
        </v-container>
    </v-main>
    </v-layout>


  <!-- <div id="contents" class="ui grid padded">
    <nav
      ref="sidebar"
      class="three wide column blue"
      :style="{ height: higherHeightPx }"
    >
      <SideMenu />
    </nav>

    <main id="main" ref="main" class="twelve wide column">
      <nav v-if="route.meta.urls" class="ui breadcrumb">
        <span v-for="url in route.meta.urls" :key="url">
          <router-link :to="url.url" class="section">
            {{ url.title }}
          </router-link>
          <i class="right angle icon divider"></i>
        </span>
        <span class="active section">{{ route.meta.title }}</span>
      </nav>

      <h1 class="ui header">
        {{ route.meta.title }}
        <div class="sub header">
          {{ route.meta.description }}
        </div>
      </h1>

      <section class="mt-2 ui segment">
        <router-view />
      </section>

       <Loading
        :active="isLoading"
        :can-cancel="false"
        :is-full-page="true"
      />
    </main>
  </div>  -->
</template>

<style scoped>
/* .ui.grid {
  height: 100%;
  min-height: 100%;
}

.ui.grid .blue.column {
  margin-right: 3em;
  padding-left: 0;
  padding-right: 0;
  background-color: #1e50a2 !important;
  height: 100vh;
  padding-top: 0;
}

#contents {
  background-color: whitesmoke;
} */

.v-container {
  max-width: 1150px !important;
}
</style>
