<script setup>
    import paginate from "vuejs-paginate-next";
    import {reactive, ref, watch } from "vue";
    const list = defineModel('list', {type:Array, required:true, default:() => []});
    const emit = defineEmits(['loadPage']);
    const execEmit = () => {
        emit('loadPage', { 'response': currentList.value });
    }
    const perPage = defineModel('perPage', {type:Number, required:false, default:10});

    // 現在表示されているページ数
    const currentPage = ref(1);
    // 最後のページ数
    const lastPage = ref(0);

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
        pageCount();
        clickCallback(currentPage.value);
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
        first-last-button
        first-button-text="最初へ"
        last-button-text="最後へ"
        v-if="lastPage.value != 0"
    >
    </paginate>
</template>
<style>
/* Write your own CSS for pagination */
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
</style>