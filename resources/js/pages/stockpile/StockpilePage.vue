<script>
import MessageArea from "../component/MessageArea.vue";
import Loading from "vue-loading-overlay";
import axios from "axios";
import ListPagination from "../component/ListPagination.vue";
import FoilTag from "../component/tag/FoilTag.vue";
import ConditionTag from "../component/tag/ConditionTag.vue";
import ImageModal from "../component/ImageModal.vue";
import CardLayout from "../component/CardLayout.vue";

export default {
    components: {
        loading: Loading,
        "message-area": MessageArea,
        pagination: ListPagination,
        foiltag: FoilTag,
        condition: ConditionTag,
        cardlayout:CardLayout,
        "image-modal":ImageModal
    },
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
            this.$store.dispatch("message/clear");
            this.$store.dispatch("clearCards");
            this.isLoading = true;
            console.log("start search stockpile");
            const query = {
                params: {
                    card_name: this.cardname,
                    set_name: this.setname,
                },
            };
            await axios
                .get("/api/stockpile", query)
                .then((response) => {
                    console.log(response.data);
                    let data = response.data;
                    this.stock = data;
                    this.$store.dispatch("setCard", this.stock);
                })
                .catch((e) => {
                    let data = e.response.data;
                    console.error(data);
                    this.$store.dispatch(
                            "message/error",
                            data.detail
                        );
                })
                .finally(() => {
                    this.isLoading = false;
                    console.log("end search stockpile");
                });
        },
    },
};
</script>

<template>
    <message-area/>
    <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="five wide field">
                <label>カード名(一部)</label>
                <input v-model="cardname" type="text">
            </div>

            <div class="four wide field">
                <label for="">セット名(ex:ローウィン)</label>
                <div class="ui input">
                    <input v-model="setname" type="text">
                </div>
            </div>
            <div class="field">
                <label style="visibility: hidden">検索ボタン</label>
                <button
                id="search"
                :class="{ disabled: cardname == '' && setname == '' }"
                    class="ui button teal ml-1"
                    @click="search"
                >
                    検索する
                </button>
            </div>
        </div>
    </article>
    <article class="mt-2" v-if="stock.length != 0">
        <h2 class="ui medium dividing header">
            件数：{{ stock.length }}件
        </h2>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th>在庫ID</th>
                    <th class="six wide">カード</th>
                    <th class="two wide center aligned">言語</th>
                    <th class="two wide center aligned">状態</th>
                    <th class="two wide center aligned">枚数</th>
                    <th class="right aligned">最終更新日</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(s, index) in $store.getters.sliceCard"
                    :key="index"
                >
                    <td>{{ s.id }}</td>
                    <td>
                            <h4 class="ui image header">
                                <img    
                                :src="s.card.image_url"
                                class="ui mini rounded image"
                                @click="$refs.modal[index].showImage(s.id)"
                            >
                            <div class="content">
                                {{ s.card.name}}
                            <div v-if="s.card.promotype.id != '1'">&#8810;{{s.card.promotype.name}}&#8811;</div>
                            <foiltag :isFoil="s.card.foil.is_foil" :foiltype="s.card.foil.name"/>
                            <div class="sub header">{{ s.card.exp.name }}&#91;{{s.card.exp.attr}}&#93;&#35;{{ s.card.number }}</div>
                        </div>
                        <image-modal
                        :url="s.card.image_url"
                        :id="s.id"
                        ref="modal"
                        />
                    </h4>
                    </td>
                    <td class="center aligned">{{ s.lang }}</td>
                    <td class="center aligned">
                        <condition :name="s.card.condition"/>
                    </td>
                    <td class="center aligned">{{ s.quantity }}</td>
                    <td class="right aligned">{{ s.updated_at }}</td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination/>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
        <loading
            :active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
        />
    </article>
</template>