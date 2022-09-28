<template>
    <div class="ui form mt-2 segment">
        <div class="three fields">
            <div class="ui action input field">
                <input
                    type="text"
                    placeholder="エキスパンション(未実装)"
                    v-model="expansion"
                />
                <button class="ui primary button" @click="search">検索</button>
            </div>
        </div>
    </div>
</template>
<script>
import NotionCardProvider from "../../composables/NotionCardProvider";

export default {
    props: ["limitprice"],
    data() {
        return {
            expansion: "",
        };
    },
    methods: {
        async search() {
            const provider = new NotionCardProvider(this.$store);
            const query = {
                limitprice: this.limitprice,
            };
            const filtered = function (r) {
                return r.price >= query.limitprice;
            };
            provider.searchByStatus(query, filtered);
        },
    },
};
</script>
