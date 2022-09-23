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
                <th>エキスパンション</th>
                <th class="one wide">枚数</th>
                <th>色</th>
                <th class="one wide">言語</th>
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
                    {{ card.name
                    }}<label
                        class="ui horizontal teal label ml-1"
                        v-show="card.isFoil"
                        >Foil</label
                    >
                </td>
                <td>{{ card.exp.name }}</td>
                <td>{{ card.stock }}</td>
                <td>{{ card.color }}</td>
                <td>{{ card.lang }}</td>
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
<script>
import ListPagination from "./ListPagination.vue";

export default {
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
    },
    components: { pagination: ListPagination },
};
</script>
