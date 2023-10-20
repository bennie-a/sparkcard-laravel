<template>
    <div class="ui form mt-2 segment">
        <div class="three fields">
            <div class="ui action input field">
                <input
                    type="text"
                    placeholder="エキスパンション(未実装)"
                    v-model="expansion"
                />
                <button class="ui teal button" @click="search">検索</button>
            </div>
        </div>
    </div>
</template>
<script>
import NotionCardProvider from "../../composables/NotionCardProvider";

export default {
    props: ["limitprice", 'status'],
    data() {
        return {
            expansion: "",
        };
    },
    methods: {
        async search() {
            const provider = new NotionCardProvider(this.$store);
            const query = {
                params:{
                    price: this.limitprice,
                    status:this.status
                }
            };

            provider.searchByStatus(query);
        },
    },
};
</script>
