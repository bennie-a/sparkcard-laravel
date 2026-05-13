<script setup>
import { ref, onMounted, watch, nextTick, computed } from 'vue'
import { useRoute } from 'vue-router'
import SideMenu from "../pages/component/SideMenu.vue"
import Loading from "vue-loading-overlay"
import "vue-loading-overlay/dist/css/index.css"

import { LoadingStore } from '@/stores/loading/Loading'
import { storeToRefs } from "pinia"

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

// ----------------------
// layout state
// ----------------------
const initHeight = ref(0)
const sidebarHeight = ref(0)
const mainHeight = ref(0)
const isMounted = ref(false)

// ----------------------
// computed
// ----------------------
const higherHeightPx = computed(() => {
  return isMounted.value
    ? Math.max(sidebarHeight.value, mainHeight.value) + "px"
    : null
})

// ----------------------
// route watch（旧watch置き換え）
// ----------------------
watch(route, () => {
  // Vuex使ってるならそのまま
  // ※ ここはPiniaに寄せた方がいい
  // store.dispatch("clearCards")
  // store.dispatch("clearMessage")

  mainHeight.value = initHeight.value
})

// ----------------------
// mounted処理
// ----------------------
// onMounted(() => {
//   nextTick(() => {
//     if (!main.value) return

//     const resizeObserver = new ResizeObserver(() => {
//       mainHeight.value = main.value.clientHeight
//     })

//     resizeObserver.observe(main.value)

//     sidebarHeight.value = sidebar.value?.clientHeight || 0
//     mainHeight.value = main.value.clientHeight
//     initHeight.value = mainHeight.value

//     isMounted.value = true
//   })
// })
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
        <v-navigation-drawer :width="300">
            <SideMenu />
        </v-navigation-drawer>
        <v-main class="mt-4 d-flex align-center justify-center" min-height="95vh">
      <v-container>
        <h1>{{ route.meta.title }}</h1>
          <section class="mt-6 ">
           <router-view />
          </section>
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
.ui.grid {
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
}

.v-container {
  max-width: 1150px !important;
}
</style>
