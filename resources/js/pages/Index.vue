<script>
import axios from "axios";

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
                    let filterd = response.data.filter((d) => {
                        return d.price > 0;
                    });
                    filterd.forEach((d) => {
                        this.cards.push(d);
                    });
                    this.loading = false;
                })
                .catch((e) => {
                    console.error(e);
                    this.loading = false;
                });
        },
        async regist() {
            console.log("Notion Resist Start");
            await Promise.all(
                this.cards.map(async (c) => {
                    let query = {
                        name: c.name,
                        enname: c.enname,
                        index: c.index,
                        price: c.price,
                        attr: "DMU",
                    };
                    await axios
                        .post("api/notion/card", query)
                        .then((response) => {
                            if (response.status == 200) {
                                console.log("登録完了");
                            } else {
                                console.log(response.status);
                            }
                        });
                })
            );
        },
    },
};
</script>

<template>
    <h1 class="display-6">カード登録</h1>
    <small class="text-muted"
        >Wisdom
        Guildからカード情報を取得して、Notionの商品管理ボードに登録します。</small
    >
    <section class="mt-5">
        <button class="btn btn-primary" @click="search">検索する</button>
        <div v-show="loading">
            <div class="loader"></div>
            <p class="text-center h3">Now loading...</p>
        </div>
        <div
            class="alert alert-success mt-2"
            role="alert"
            v-if="cards.length != 0"
        >
            {{ cards.length }}件取得しました。
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
        <div class="text-center" v-if="cards.length != 0">
            <button class="btn btn-primary" @click="regist">
                Notionに登録する
            </button>
        </div>
    </section>
</template>
