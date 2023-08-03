<template>
    <paginate
        :v-model="page"
        :page-count="getPageCount"
        :page-range="3"
        :margin-pages="2"
        :click-handler="clickCallback"
        :prev-text="'Prev'"
        :next-text="'Next'"
        :prev-class="'item'"
        :next-class="'item'"
        :container-class="'ui pagination menu'"
        :page-class="'item'"
        v-if="length != 0"
    >
    </paginate>
</template>
<script>
import Paginate from "vuejs-paginate-next";

export default {
    components: {
        paginate: Paginate,
    },

    data() {
        return {
            perPage: this.count,
        };
    },
    props: {
        count: { type: Number, default: 10 },
    },
    computed: {
        page: function () {
            return this.$store.getters.getCurrentPage;
        },
        length: function () {
            return this.$store.getters.cardsLength;
        },
        getPageCount: function () {
            this.$store.dispatch("perPage", this.count);
            const length = this.$store.getters.cardsLength;
            return Math.ceil(length / this.count);
        },
    },
    methods: {
        clickCallback(pageNum) {
            this.$store.dispatch("setCurrentPage", pageNum);
        },
    },
};
</script>
<style scoped>
/* Write your own CSS for pagination */
.pagination.menu {
    padding-left: 0;
}

.pagination.menu > .item {
    cursor: pointer;
}
</style>
