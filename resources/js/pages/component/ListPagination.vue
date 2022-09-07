<template>
    <paginate
        :v-model="page"
        :page-count="getPageCount"
        :page-range="3"
        :margin-pages="2"
        :click-handler="clickCallback"
        :prev-text="'Prev'"
        :next-text="'Next'"
        :prev-class="'page-item'"
        :next-class="'page-item'"
        :container-class="'pagenation'"
        :page-class="'page-item'"
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
            perPage: 10,
        };
    },
    computed: {
        page: function () {
            return this.$store.getters.getCurrentPage;
        },
        length: function () {
            return this.$store.getters.cardsLength;
        },
        getPageCount: function () {
            const length = this.$store.getters.cardsLength;
            return Math.ceil(length / this.perPage);
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
.pagenation {
    padding-bottom: 10px;
    display: flex;
    justify-content: center;
    list-style-type: none;
}
.page-item {
    border: 1px solid #ccc;
    cursor: pointer;
    padding: 0.6em;
}
.page-item > a {
    display: inline-block;
}

li.page-item.active {
    background-color: #2766cc;
}

li.page-item.active > a {
    color: white;
}
</style>
