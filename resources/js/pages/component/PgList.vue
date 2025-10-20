<script setup>
import paginate from "vuejs-paginate-next";
import { reactive, ref, computed, watch } from "vue";

const list = defineModel("list", { type: Array, required: true, default: () => [] });
const perPage = defineModel("perPage", { type: Number, required: false, default: 10 });
const emit = defineEmits(["loadPage"]);

// 現在ページ番号
const currentPage = ref(1);

// 最終ページ番号
const lastPage = ref(0);

// 現在表示中のリスト
const currentList = ref([]);

// 表示範囲（例: 1〜5件）
const start = ref(0);
const end = ref(0);

// 現在のページに応じてスライス処理
const clickCallback = (pageNum) => {
  currentPage.value = pageNum;

  const total = list.value.length;
  const from = (pageNum - 1) * perPage.value;
  const to = Math.min(pageNum * perPage.value, total);

  start.value = from + 1;
  end.value = to;

  currentList.value = list.value.slice(from, to);
  emit("loadPage", { response: currentList.value });
};

// ページ総数を算出
const pageCount = () => {
  lastPage.value = Math.ceil(list.value.length / perPage.value);
};

// listまたはperPageが変更されたときに再計算
watch([() => list.value, () => perPage.value], () => {
  pageCount();
  clickCallback(currentPage.value);
});

// 初期化
watch(
  () => currentPage.value,
  () => clickCallback(currentPage.value),
  { immediate: true }
);

// ページ範囲表示用テキスト
const pageRangeText = computed(() => {
  if (list.value.length === 0) return "";
  return `${start.value}-${end.value}件 / ${list.value.length}件`;
});
</script>

<template>
    <span id="number" class="mr-2">{{ pageRangeText }}</span>
    <paginate
      v-model="currentPage"
      :page-count="lastPage"
      :click-handler="clickCallback"
      prev-text="<"
      next-text=">"
      :page-range="3"
      :margin-pages="2"
      container-class="ui pagination menu"
      page-class="item"
      prev-class="item"
      next-class="item"
      page-link-class="page-link"
      first-last-button
      first-button-text="最初へ"
      last-button-text="最後へ"
      v-if="lastPage !== 0"
    />
</template>

<style scoped>
.pagination.menu {
  padding-left: 0;
}

.pagination.menu .item {
  cursor: pointer;
}

.page-link {
  display: block;
}
.page-link:hover {
  cursor: pointer;
}

.pagination-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

#number {
  font-size: 1.1rem;
}
</style>
