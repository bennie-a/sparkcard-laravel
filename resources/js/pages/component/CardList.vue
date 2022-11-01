<template>
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
                <th v-show="$route.path === '/logikura/newitem'">バーコード</th>
                <th v-show="$route.path === '/base/newitem'">商品ID</th>
                <th>カード番号</th>
                <th>カード名</th>
                <th v-if="this.exp">エキスパンション</th>

                <th class="one wide">枚数</th>
                <th v-if="this.isNotion" class="one wide">状態</th>
                <th>色</th>
                <th v-if="this.isNotion" class="one wide">言語</th>
                <th class="one wide">価格</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="card in getCards">
                <td>
                    <input
                        type="checkbox"
                        v-model="selectedCard"
                        :value="card.id"
                        @change="checked"
                    />
                </td>
                <td v-show="$route.path === '/logikura/newitem'">
                    {{ card.barcode }}
                </td>
                <td v-show="$route.path === '/base/newitem'">
                    {{ card.baseId }}
                </td>
                <td>{{ card.index }}</td>
                <td>
                    <button
                        class="ui button nocolor"
                        @click="showImage(card.index)"
                    >
                        {{ card.name
                        }}<label
                            class="ui horizontal teal label ml-1"
                            v-show="card.isFoil"
                            >Foil</label
                        >
                    </button>
                    <div class="ui tiny modal" v-bind:id="card.index">
                        <i class="close icon"></i>
                        <div class="image content">
                            <img v-bind:src="card.image" class="image" />
                        </div>
                    </div>
                </td>
                <td v-if="this.exp">{{ card.exp.name }}</td>

                <td v-if="this.isNotion">{{ card.stock }}枚</td>
                <td v-if="this.isNotion == false">
                    <div
                        class="ui right labeled input one wide"
                        :class="{
                            disabled: !this.selectedCard.includes(card.id),
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
                <td v-if="this.isNotion">
                    <div class="ui label" :class="condiColor(card.condition)">
                        {{ card.condition }}
                    </div>
                </td>
                <td>{{ card.color }}</td>
                <td v-if="this.isNotion">{{ card.lang }}</td>
                <td>{{ card.price }}円</td>
            </tr>
        </tbody>
        <tfoot v-if="this.$store.getters.cardsLength != 0">
            <tr>
                <th colspan="9">
                    <pagination></pagination>
                </th>
            </tr>
        </tfoot>
    </table>
</template>
<style scoped>
img.image {
    margin: 0 auto;
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
<script>
import ListPagination from "./ListPagination.vue";

export default {
    props: {
        exp: { type: Boolean, default: false },
        isNotion: { type: Boolean, default: false },
    },
    data() {
        return {
            selectedCard: [],
            isAll: false,
        };
    },
    mounted: function () {
        // this.selectedCard = [];
    },
    computed: {
        loading: function () {
            return this.$store.getters.isload;
        },
        getCards: function () {
            return this.$store.getters.sliceCard;
        },
        condiColor: function () {
            return (condition) => {
                const colors = {
                    NM: "teal",
                    "NM-": "blue",
                    "EX+": "purple",
                    EX: "brown",
                };
                return colors[condition];
            };
        },
        isChecked: function ($id) {
            console.log($id);
        },
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
    components: { pagination: ListPagination },
};
</script>
