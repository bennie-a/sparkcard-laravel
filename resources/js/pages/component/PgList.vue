<script setup>
    import paginate from "vuejs-paginate-next";
    import {reactive, ref, watch } from "vue";
    const list = defineModel('list', {type:Array, required:true});
    const emit = defineEmits(['loadPage']);
    const execEmit = () => {
        emit('loadPage', { 'response': currentList.value });
    }

    const currentPage = ref(1);
    const lastPage = ref(0);
    const perPage = ref(10);

    const currentList = reactive([]);

    // ページ番号をクリックしたときのイベント
    const clickCallback = (pageNum) => {
        currentPage.value = pageNum;
        currentList.value = list.value;
        let current = pageNum * perPage.value;
        let start = current - perPage.value;
        
        currentList.value = list.value.slice(start, current);
        execEmit();
    }

    // paginationのページ数を算出する。
    const pageCount = () => {
        lastPage.value = Math.ceil(list.value.length / perPage.value);
    }

    watch(() => list.value, () => {
        if (lastPage.value === 0) {
            pageCount();
            clickCallback(currentPage.value);
        }
    });

</script>
<template>
        <paginate
        v-model="currentPage"
        :page-count="lastPage"
        :click-handler="clickCallback"
        :prev-text="'<'"
        :next-text="'>'"
        :page-range="3"
            :margin-pages="2"
        :container-class="'ui pagination menu'"
        :page-class="'item'"
        :prev-class="'item'"
        :next-class="'item'"
        :page-link-class="'page-link'"
    >
    </paginate>
</template>
<style scoped>
/* Write your own CSS for pagination */
.pagination.menu {
    padding-left: 0;
}

.pagination.menu .item {
    cursor: pointer;
}

a.page-link:hover {
    cursor: pointer  !important;
}
</style>