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
        isDisabled: function () {
            let selected = this.$store.getters["csvOption/selectedList"];
            return selected.length == 0;
        },
    },
    methods: {
        async search() {
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
                    this.$store.dispatch(
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
        showRegist() {
            $("#regist").modal("show");
        },
        // Notionにカード情報を登録する。
        async regist() {
            this.$store.dispatch("setLoad", true);
            console.log("Notion Resist Start");
            const card = this.$store.getters.card;
            const checkbox = this.$store.getters["csvOption/selectedList"];
            console.log(checkbox);
            const filterd = card.filter((c) => {
                return checkbox.includes(c.id);
            });
            await Promise.all(
                filterd.map(async (c) => {
                    let query = {
                        name: c.name,
                        enname: c.enname,
                        index: c.index,
                        price: c.price,
                        attr: this.set,
                        color: c.color,
                        imageurl: c.image,
                        stock: c.stock,
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
            $("#regist").modal("hide");
            this.$store.dispatch("setSuccessMessage", "登録が完了しました。");
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
    <div class="mt-1">
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
            <option value="less">無色</option>
            <option value="artifact">アーティファクト</option>
            <option value="land">土地</option>
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
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <button
            class="ui purple button"
            @click="showRegist"
            :class="{ disabled: isDisabled }"
        >
            Notionに登録する
        </button>
        <div id="regist" class="ui tiny modal">
            <div class="header">Notice</div>
            <div class="content">登録してもよろしいですか?</div>
            <div class="actions">
                <button class="ui cancel button">
                    <i class="close icon"></i>キャンセル
                </button>
                <button class="ui primary button" @click="regist">
                    <i class="checkmark icon"></i>登録する
                </button>
            </div>
        </div>
    </div>
    <card-list></card-list>
    <now-loading></now-loading>
</template>
<style></style>
