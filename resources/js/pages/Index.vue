<script>
export default {
    data() {
        return { cards: [], loading: false };
    },
    methods: {
        async search() {
            this.loading = true;
            console.log("wisdom guild search");
            this.cards.splice(0);
            await axios
                .get("/api/wisdom")
                .then((response) => {
                    response.data.forEach((d) => {
                        this.cards.push(d);
                    });
                    this.loading = false;
                })
                .catch((e) => {
                    console.error(e);
                    this.loading = false;
                });
        },
    },
};
</script>

<template>
    <h1 class="display-6">カード登録</h1>
    <small class="text-muted">Wisdom Guildからカード情報を持ってきます</small>
    <section class="mt-5">
        <button class="btn btn-primary" @click="search">検索する</button>
        <div v-show="loading">
            <div class="loader"></div>
            <p class="text-center h3">Now loading...</p>
        </div>
        <table v-show="!loading" class="table table-striped mt-3">
            <thead>
                <tr>
                    <th scope="col">カード番号</th>
                    <th scope="col">カード名</th>
                    <th scope="col">英語名</th>
                    <th scope="col">価格</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(card, index) in cards">
                    <td>{{ card.index }}</td>
                    <td>{{ card.name }}</td>
                    <td>{{ card.enname }}</td>
                    <td>{{ card.price }}</td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
