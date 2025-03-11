<template>
    <article class="mt-2" v-if="getCards.length > 0">
        <h2 class="ui medium dividing header">件数：{{ getCards.length }}件</h2>
        <table v-show="!loading" class="ui table striped">
            <thead>
                <tr>
                    <th class="one wide">
                        <input
                            type="checkbox"
                            id="all"
                            v-model="isAll"
                            @change="allChecked"
                        />
                    </th>
                    <th class="six wide left aligned">カード</th>
                    <th class="two wide center aligned">数量</th>
                    <th v-if="isNotion" class="two wide center aligned">
                        状態
                    </th>
                    <th v-if="isNotion" class="two wide center aligned">
                        言語
                    </th>
                    <th class="left aligned">価格</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(card, index) in getCards" :key="index">
                    <td>
                        <input
                            type="checkbox"
                            v-model="selectedCard"
                            :value="card.id"
                            @change="checked"
                        />
                    </td>
                    <td>
                        <h4 class="ui image header">
                            <img
                                :src="card.image"
                                class="ui mini rounded image"
                                @click="
                                    $refs.modal[index].showImage(card.index)
                                "
                            />
                            <div class="content">
                                {{ card.name
                                }}<foiltag :isFoil="card.isFoil"></foiltag>
                                <div class="sub header">
                                    {{ card.exp.name }}
                                </div>
                            </div>
                            <image-modal
                                :url="card.image"
                                :id="card.index"
                                ref="modal"
                            ></image-modal>
                        </h4>
                    </td>

                    <td v-if="isNotion" class="center aligned">
                        {{ card.stock }}
                    </td>
                    <td v-if="isNotion == false" class="center aligned">
                        <div
                            class="ui right labeled input one wide"
                            :class="{
                                disabled: !selectedCard.includes(card.id),
                            }"
                        >
                            <input
                                type="number"
                                step="1"
                                min="0"
                                class="text-stock"
                                v-model="card.stock"
                            />
                            <div class="ui basic label">枚</div>
                        </div>
                    </td>
                    <td v-if="isNotion" class="center aligned">
                        <condition :name="card.condition"></condition>
                    </td>
                    <td v-if="isNotion" class="center aligned">
                        {{ card.lang }}
                    </td>
                    <td>{{ card.price }}円</td>
                </tr>
            </tbody>
            <tfoot
                class="full-width"
                v-if="$store.getters.cardsLength != 0"
            >
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination></pagination>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
    </article>
</template>
<script>
import ListPagination from "./ListPagination.vue";
import FoilTag from "./tag/FoilTag.vue";
import ConditionTag from "./tag/ConditionTag.vue";
import ImageModal from "./ImageModal.vue";

export default {
    components: {
        pagination: ListPagination,
        foiltag: FoilTag,
        condition: ConditionTag,
        ImageModal,
    },
    props: {
        exp: { type: Boolean, default: false },
        isNotion: { type: Boolean, default: false },
    },
    data() {
        return {
            selectedCard: [],
            isAll: false,
            // keyword: "",
            // fullcard: [],
        };
    },
    computed: {
        loading: function () {
            return this.$store.getters.isload;
        },
        getCards: function () {
            return this.$store.getters.sliceCard;
        },

    },
    mounted: function () {
        // this.selectedCard = [];
    },
    methods: {
        allChecked: function () {
            if (this.isAll) {
                this.$store.getters.card.forEach((c) => {
                    this.selectedCard.push(c.id);
                });
            } else {
                this.selectedCard.splice(0);
            }
        },
        checked: function () {
            this.$store.dispatch("csvOption/setSelected", this.selectedCard);
            if (this.selectedCard.length === this.$store.getters.cardsLength) {
                this.isAll = true;
            } else {
                this.isAll = false;
            }
        },
        showImage: function (id) {
            const selecterId = `#${id}`;
            console.log(selecterId);
            $(selecterId).modal("show");
        },
    },
};
</script>
<style scoped>
img.image {
    margin: 0 auto;
    max-width: 400px;
}
.ui.pagination.menu .item {
    cursor: pointer;
}
.ui.button.nocolor {
    background-color: transparent;
    font-weight: bold;
    text-decoration: underline;
    padding: 0;
    margin: 0;
    border: 0;
    color: #2185d0;
}

input.text-stock {
    width: 6vw;
}
</style>
