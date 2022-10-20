<script>
import axios from "axios";
import NowLoading from "./component/NowLoading.vue";
import CardList from "./component/CardList.vue";
import MessageArea from "./component/MessageArea.vue";

export default {
    data() {
        return {
            cards: [],
            perPage: 10,
            currentPage: 1,
            message: "",
            set: "",
            color: "red",
        };
    },
    computed: {
        getCards: function () {
            let current = this.currentPage * this.perPage;
            let start = current - this.perPage;
            return this.cards.slice(start, current);
        },
        getPageCount: function () {
            return Math.ceil(this.cards.length / this.perPage);
        },
    },
    methods: {
        async search() {
            // $("#search").addClass("loading disabled");
            this.$store.dispatch("setLoad", true);
            console.log("wisdom guild search");
            this.$store.dispatch("clearCards");
            const query = {
                params: {
                    set: this.set,
                    color: this.color,
                },
            };
            await axios
                .get("/api/wisdom", query)
                .then((response) => {
                    let filterd = response.data.filter((d) => {
                        return d.price > 0;
                    });
                    this.$store.dispatch("setCard", filterd);

                    this.$store.dispatch("setLoad", false);
                    store.dispatch(
                        "setSuccessMessage",
                        filterd.length + "件取得しました。"
                    );
                })
                .catch((e) => {
                    console.error(e);
                    store.dispatch(
                        "message/error",
                        "予期せぬエラーが発生しました。"
                    );
                    this.$store.dispatch("setLoad", false);
                });
            $("#search").removeClass("loading disabled");
        },
        async regist() {
            this.$store.dispatch("setLoad", true);
            console.log("Notion Resist Start");
            await Promise.all(
                this.cards.map(async (c) => {
                    let query = {
                        name: c.name,
                        enname: c.enname,
                        index: c.index,
                        price: c.price,
                        attr: "DMU",
                        color: c.color,
                        imageurl: c.imageurl,
                    };
                    await axios
                        .post("api/notion/card", query)
                        .then((response) => {
                            if (response.status == 200) {
                                console.log(query.name + ":登録完了");
                            } else {
                                console.log(response.status);
                            }
                        });
                })
            );
            this.$store.dispatch("setLoad", false);
            this.message = "登録が完了しました。";
        },
        clickCallback(pageNum) {
            this.currentPage = Number(pageNum);
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
    },
};
</script>

<template>
    <message-area></message-area>
    <div>
        <select v-model="set" class="ui dropdown">
            <option value="">選択してください</option>
            <option value="DMU">団結のドミナリア(DMU)</option>
            <option value="WAR">灯争大戦(WAR)</option>
        </select>
        <select v-model="color" class="ui dropdown">
            <option value="red">赤</option>
            <option value="white">白</option>
            <option value="black">黒</option>
            <option value="green">緑</option>
            <option value="blue">青</option>
            <option value="multi">多色</option>
        </select>
        <button
            id="search"
            class="ui button purple ml-1"
            @click="search"
            :class="{ disabled: set == '' }"
        >
            検索する
        </button>
    </div>

    <card-list imgUrl></card-list>
    <now-loading></now-loading>
    <div class="text-center" v-if="cards.length != 0">
        <button class="ui purple button" @click="regist">
            Notionに登録する
        </button>
    </div>
</template>
<style></style>
