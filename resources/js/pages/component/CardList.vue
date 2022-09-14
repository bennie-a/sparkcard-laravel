<template>
    <pagination></pagination>
    <table v-show="!loading" class="ui table striped">
        <thead>
            <tr>
                <th v-show="$route.path === '/logikura/newitem'">バーコード</th>
                <th>カード番号</th>
                <th>カード名</th>
                <th>枚数</th>
                <th>色</th>
                <th>言語</th>
                <th>価格</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="card in getCards">
                <!-- <td>
                    <input
                        type="checkbox"
                        v-model="selectedCard"
                        :value="card.id"
                    />
                </td> -->
                <td v-show="$route.path === '/logikura/newitem'">
                    {{ card.barcode }}
                </td>
                <td>{{ card.index }}</td>
                <td>{{ card.name }}</td>
                <td>{{ card.stock }}</td>
                <td>{{ card.color }}</td>
                <td>{{ card.lang }}</td>
                <td>{{ card.price }}円</td>
            </tr>
        </tbody>
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
    components: { pagination: ListPagination },
};
</script>
