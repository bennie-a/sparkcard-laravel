<template>
    <message-area></message-area>
    <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="five wide field">
                <label>カード名(一部)</label>
                <input type="text" v-model="cardname" />
            </div>

            <div class="five wide field">
                <label for="">セット名(一部)</label>
                <div class="ui input">
                    <input type="text" v-model="setname" />
                </div>
            </div>
            <div class="field">
                <label style="visibility: hidden">検索ボタン</label>
                <button
                    id="search"
                    class="ui button teal ml-1"
                    @click="search"
                    :class="{ disabled: cardname == '' && setname == '' }"
                >
                    検索する
                </button>
            </div>
        </div>
    </article>
    <article>
        <h2 class="ui medium dividing header">
            件数：{{ this.stock.length }}件
        </h2>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="six wide">カード</th>
                    <th class="two wide center aligned">言語</th>
                    <th class="two wide center aligned">状態</th>
                    <th class="two wide center aligned">枚数</th>
                    <th class="right aligned">最終更新日</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="s in this.$store.getters.sliceCard">
                    <td>
                        <h4 class="ui image header">
                            <img
                                v-bind:src="s.image_url"
                                class="ui mini rounded image"
                            />
                            <div class="content">
                                {{ s.cardname
                                }}<foiltag :isFoil="s.isFoil"></foiltag>
                                <div class="sub header">{{ s.setname }}</div>
                            </div>
                        </h4>
                    </td>
                    <td class="center aligned">{{ s.language }}</td>
                    <td class="center aligned">{{ s.condition }}</td>
                    <td class="center aligned">{{ s.quantity }}</td>
                    <td class="right aligned">{{ s.updated_at }}</td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination></pagination>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
        <loading
            :active.sync="isLoading"
            :can-cancel="true"
            :is-full-page="true"
        ></loading>
    </article>
</template>

<script>
import MessageArea from "../component/MessageArea.vue";
import Loading from "vue-loading-overlay";
import axios from "axios";
import ListPagination from "../component/ListPagination.vue";
import FoilTag from "../component/FoilTag.vue";

export default {
    data() {
        return {
            cardname: "",
            setname: "",
            isLoading: false,
            stock: [],
        };
    },
    methods: {
        async search() {
            let self = this;
            self.isLoading = true;
            console.log("search stockpile");
            const query = {
                params: {
                    card_name: this.cardname,
                    set_name: this.setname,
                },
            };
            await axios
                .get("/api/stockpile", query)
                .then((response) => {
                    let data = response.data;
                    this.stock = data;
                    this.$store.dispatch("setCard", this.stock);
                })
                .catch((e) => {
                    console.error(e);
                })
                .finally(() => {
                    self.isLoading = false;
                });
        },
    },
    components: {
        loading: Loading,
        "message-area": MessageArea,
        pagination: ListPagination,
        foiltag: FoilTag,
    },
};
</script>
