<template>
    <message-area></message-area>
    <div class="ui form mt-2">
        <div class="five fields">
            <div class="field">
                <label for="">ステータス</label>
                <select class="ui fluid dropdown" v-model="selectedStatus">
                    <option v-for="status in getStatus" v-bind:value="status">
                        {{ status }}
                    </option>
                </select>
            </div>
            <div class="field">
                <label for="">価格</label>
                <div class="two fields">
                    <div class="field">
                        <input
                            type="text"
                            placeholder="価格"
                            v-model="price"
                            @input="
                                (event) =>
                                    (value = event.target.value.replace(
                                        /[^0-9]/g,
                                        ''
                                    ))
                            "
                        />
                    </div>
                    <div>
                        <select
                            class="ui fluid search dropdown"
                            v-model="isMore"
                        >
                            <option value="true">以上</option>
                            <option value="false">未満</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label for="" style="visibility: hidden">ボタン</label>
                <button class="ui purple button ml-1" @click="search">
                    検索する
                </button>
            </div>
        </div>
    </div>
    <div class="ui segment mt-3" v-if="$store.getters.cardsLength != 0">
        <h2 class="ui small header">次のステータスに一括変更する</h2>
        <div class="flex">
            <select class="ui dropdown" v-model="updateStatus">
                <option v-for="status in getStatus" v-bind:value="status">
                    {{ status }}
                </option>
            </select>

            <button class="ui purple button" @click="update">
                ステータスを更新する
            </button>
        </div>
    </div>
    <card-list exp isNotion></card-list>
    <now-loading></now-loading>
</template>
<style scoped>
div.flex {
    display: flex;
    align-items: center;
    column-gap: 1rem;
}
</style>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import { NOTION_STATUS } from "../../cost/NotionStatus";
import NotionCardProvider from "../../composables/NotionCardProvider";

export default {
    data() {
        return {
            selectedStatus: NOTION_STATUS.logikura,
            updateStatus: NOTION_STATUS.logikura,
            price: 0,
            isMore: false,
            error: "",
        };
    },
    computed: {
        getStatus: function () {
            const status = [];
            Object.keys(NOTION_STATUS).forEach((key) => {
                status.push(NOTION_STATUS[key]);
            });
            return status;
        },
    },
    methods: {
        async search() {
            this.$store.dispatch("search/status", this.selectedStatus);
            const query = {
                price: Number(this.price),
                isMore: Boolean(this.isMore),
            };
            const filtered = function (r) {
                if (query.price == 0) {
                    return true;
                }
                if (query.isMore) {
                    return r.price >= query.price;
                } else {
                    return r.price < query.price;
                }
            };
            const provider = new NotionCardProvider(this.$store);
            provider.searchByStatus(query, filtered);
        },
        async update() {
            this.$store.dispatch("setLoad", true);
            console.log("Status Update...");
            console.log(this.updateStatus);
            const task = new AxiosTask(this.$store);
            const success = function (response, store, query) {
                const result = response.data;
            };
            const fail = function (e, store, query) {
                console.error(e);
                store.dispatch(["message/error", e.message]);
            };
            const card = this.$store.getters.card;
            const checkbox = this.$store.getters["csvOption/selectedList"];
            const filterd = card.filter((c) => {
                return checkbox.includes(c.id);
            });

            await Promise.all(
                filterd.map(async (c) => {
                    let url = "/notion/card/" + c.id;
                    let query = { status: this.updateStatus };
                    await task.patch(url, query, success, fail);
                })
            );
            this.$store.dispatch("setLoad", false);
            this.$store.dispatch("setSuccessMessage", "更新が完了しました。");
        },
    },
    watch: {
        price: function () {
            const pattern = /[a-zA-Z]/g; // 半角英字のみ不可
            this.price = this.price.replace(pattern, "");
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
    },
};
</script>
