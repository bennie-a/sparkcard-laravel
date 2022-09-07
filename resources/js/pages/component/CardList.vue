<template>
    <table v-show="!loading" class="ui table striped">
        <thead>
            <tr>
                <th>カード番号</th>
                <th>カード名</th>
                <th>色</th>
                <th>価格</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="card in getCards">
                <td>{{ card.index }}</td>
                <td>{{ card.name }}</td>
                <td>{{ card.color }}</td>
                <td>{{ card.price }}円</td>
            </tr>
        </tbody>
    </table>
</template>
<script>
export default {
    computed: {
        loading: function () {
            return this.$store.getters.isload;
        },
        getCards: function () {
            return this.$store.getters.card;
        },
    },
};
</script>
