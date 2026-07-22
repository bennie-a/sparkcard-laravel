<script setup>
    import { usePaginate } from './UsePaginate';
    import ListPagination from './ListPagination.vue';

    defineProps({
        items:{type:Array, required:true},
        pageCount:{type:Number, required:true},
        hitCount:{type:Number, required:true}
    });

    const page = defineModel({type:Number, required:true});
</script>
<template>
    <div v-if="hitCount > 0">
        <h2 class="text-title-medium">件数：{{ hitCount }}件( {{ page }}ページ目 / {{ pageCount }}ページ )</h2>
        <v-table class="item_list mt-4 border-thin">
            <thead>
                <tr>
                    <slot name="header"></slot>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in items" :key="index">
                    <slot name="row" :item="item"/>
                </tr>
            </tbody>
        </v-table>
        <div class="text-center mt-1">
            <ListPagination v-model="page" :length="pageCount"></ListPagination>
        </div>
    </div>
</template>
