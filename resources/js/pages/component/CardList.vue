<template>
    <table v-show="!loading" class="ui table striped">
        <thead>
            <tr>
                <th class="one wide"></th>
                <th v-show="$route.path === '/logikura/newitem'">バーコード</th>
                <th v-show="$route.path === '/base/newitem'">商品ID</th>
                <th class="two wide">カード番号</th>
                <th class="three wide">カード名</th>
                <th>枚数</th>
                <th>色</th>
                <th>言語</th>
                <th>価格</th>
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
                <td>{{ card.stock }}</td>
                <td>{{ card.color }}</td>
                <td>{{ card.lang }}</td>
                <td>{{ card.price }}円</td>
            </tr>
        </tbody>
        <tfoot v-if="this.$store.getters.cardsLength != 0">
            <tr>
                <th colspan="8">
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
    methods: {
        checked: function () {
            this.$store.dispatch("table/setSelected", this.selectedCard);
        },
    },
    components: { pagination: ListPagination },
};
</script>
