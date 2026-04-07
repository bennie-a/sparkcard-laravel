<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import DefaultLayout from '@/layout/DefaultLayout.vue'

// 空レイアウト（ログイン用）
const BlankLayout = {
  template: '<router-view />'
}

const route = useRoute()

const layout = computed(() => {
  return route.meta.layout === 'blank'
    ? BlankLayout
    : DefaultLayout
})
</script>

<template>
  <component :is="layout" />
</template>
