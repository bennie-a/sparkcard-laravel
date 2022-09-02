<script setup>
import axios from "axios";
// onMounted(async () => {
// console.log("mounted");
// // cardList.splice(0);
// await axios
//     .get("/api/wisdom")
//     .then((response) => {
//         console.log(response);
//         response.data.forEach((d) => {
//             cardList.push(d);
//         });
//     })
//     .catch((e) => {
//         console.error(e);
//     });
// });
import { ref } from "vue";
// let cards = ref([]);
</script>
<script>
export default {
    data() {
        return { cards: [{ name: "aaa", enname: "bbb" }] };
    },
    methods: {
        async search() {
            console.log("wisdom guild search");
            this.cards.splice(0);
            await axios
                .get("/api/wisdom")
                .then((response) => {
                    response.data.forEach((d) => {
                        this.cards.push(d);
                    });
                })
                .catch((e) => {
                    console.error(e);
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
        <table class="table table-striped mt-3">
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
