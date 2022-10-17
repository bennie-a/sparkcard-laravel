<script>
import axios from "axios";
import NowLoading from "./component/NowLoading.vue";
import CardList from "./component/CardList.vue";

export default {
    data() {
        return {
            cards: [],
            perPage: 10,
            currentPage: 1,
            message: "",
            set: "",
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
            const query = { params: { set: this.set } };
            // const task = new AxiosTask(this.$store);
            // task.search();
            await axios
                .get("/api/wisdom", query)
                .then((response) => {
                    let filterd = response.data.filter((d) => {
                        return d.price > 0;
                    });
                    // filterd.forEach((d) => {
                    //     this.cards.push(d);
                    // });
                    this.$store.dispatch("setCard", filterd);

                    this.$store.dispatch("setLoad", false);
                    this.message = this.filterd.length + "件見つかりました。";
                })
                .catch((e) => {
                    console.error(e);
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
    },
};
</script>

<template>
    <div class="ui info message" v-if="message != ''">
        <div class="header">
            {{ message }}
        </div>
    </div>
    <div>
        <!-- <div class="sample">
            <input
                type="radio"
                name="s3"
                id="select1"
                value="white"
                checked=""
            />
            <label for="select1">白</label>
            <input type="radio" name="s3" id="select2" value="blue" />
            <label for="select2">青</label>
            <input type="radio" name="s3" id="select3" value="red" />
            <label for="select3">赤</label>
            <input type="radio" name="s3" id="select4" value="black" />
            <label for="select4">黒</label>
            <input type="radio" name="s3" id="select5" value="green" />
            <label for="select5">緑</label>
            <input type="radio" name="s3" id="select6" value="" />
            <label for="select6">全て</label>
        </div> -->
        <select v-model="set" class="ui dropdown">
            <option value="">選択してください</option>
            <option value="DMU">団結のドミナリア(DMU)</option>
            <option value="WAR">灯争大戦(WAR)</option>
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
