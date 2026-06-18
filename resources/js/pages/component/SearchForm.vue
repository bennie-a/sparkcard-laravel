<template>
    <div class="ui form mt-2 segment">
        <div class="three fields">
            <div class="ui action input field">
                <input
                    type="text"
                    placeholder="エキスパンション名"
                    v-model="set_name"
                />
                <button class="ui teal button" @click="search">検索</button>
            </div>
        </div>
    </div>
</template>
<script>
import NotionCardProvider from "../../composables/NotionCardProvider";
import { MsgStore } from "./msg/MsgStore";
export default {
    props: ["limitprice", 'status'],
    data() {
        return {
            set_name: "",
            result:[]
        };
    },
    methods: {
        async search() {
            const msgStore = MsgStore();
            msgStore.clear();
            const provider = new NotionCardProvider();
            const query = {
                params:{
                    price: this.limitprice,
                    status:this.status,
                    set_name:this.set_name
                }
            };

            this.result = provider.searchByStatus(query);
        },
    },
};
</script>
