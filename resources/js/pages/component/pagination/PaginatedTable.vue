<script setup>
    import { usePaginate } from './UsePaginate';
    import ListPagination from './ListPagination.vue';

    defineProps({
        items:{type:Array, required:true},
        pageCount:{type:Number, required:true},
    });
    // const {
    //     page, pageCount, paginatedList, resetPage
    // } = usePaginate(items, pageCount);

</script>
<template>
    <v-table class="item_list mt-4 border-thin">
        <thead>
            <slot name="header"></slot>
        </thead>
        <tbody>
            <tr v-for="(item, index) in items" :key="index">
                <slot name="row" :item="item"/>
            </tr>
        </tbody>
        <tfoot class="full-width">
            <tr>
                <td :colspan="items.length">
                    <!-- <ListPagination v-model="page" :length="pageCount"></ListPagination> -->
                </td>
            </tr>
        </tfoot>
    </v-table>
</template>
