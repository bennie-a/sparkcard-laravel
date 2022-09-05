<script>
import axios from "axios";
import Paginate from "vuejs-paginate-next";
export default {
    components: {
        paginate: Paginate,
    },
    data() {
        return { cards: [], loading: false, perPage: 10, currentPage: 1 };
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
            this.loading = true;
            console.log("wisdom guild search");
            this.cards.splice(0);
            await axios
                .get("/api/wisdom")
                .then((response) => {
                    let filterd = response.data.filter((d) => {
                        return d.price > 50;
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
        clickCallback(pageNum) {
            this.currentPage = Number(pageNum);
        },
    },
};
</script>

<template>
    <h1 class="ui header">
        カード登録
        <div class="sub header">
            Wisdom
            Guildからカード情報を取得して、Notionの商品管理ボードに登録します。
        </div>
    </h1>
    <section class="mt-3">
        <div class="ui info message" v-if="cards.length != 0">
            <i class="close icon"></i>
            <div class="header">{{ cards.length }}件取得しました。</div>
        </div>
        <div>
            <div class="sample">
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
            </div>
            <button class="ui purple button ml-1" @click="search">
                検索する
            </button>
        </div>

        <div v-show="loading">
            <div class="loader"></div>
            <p class="text-center h3">Now loading...</p>
        </div>
        <table v-show="!loading" class="ui table striped">
            <thead>
                <tr>
                    <th>カード番号</th>
                    <th>カード名</th>
                    <th>英語名</th>
                    <th>色</th>
                    <th>画像URL</th>
                    <th>価格</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(card, index) in getCards">
                    <td>{{ card.index }}</td>
                    <td>{{ card.name }}</td>
                    <td>{{ card.enname }}</td>
                    <td>{{ card.color }}</td>
                    <td>{{ card.imageurl }}</td>
                    <td>{{ card.price }}円</td>
                </tr>
            </tbody>
        </table>
        <paginate
            :v-model="page"
            :page-count="getPageCount"
            :page-range="3"
            :margin-pages="2"
            :click-handler="clickCallback"
            :prev-text="'Prev'"
            :next-text="'Next'"
            :prev-class="'page-item'"
            :next-class="'page-item'"
            :container-class="'pagenation'"
            :page-class="'page-item'"
            v-if="cards.length != 0"
        >
        </paginate>
        <div class="text-center" v-if="cards.length != 0">
            <button class="ui purple button" @click="regist">
                Notionに登録する
            </button>
        </div>
    </section>
</template>
<style>
.mt-3 {
    margin-top: 3rem;
}

.mr-2 {
    margin-right: 2rem;
}
.ml-1 {
    margin-left: 1rem !important;
}

/* Write your own CSS for pagination */
.pagenation {
    padding-bottom: 10px;
    display: flex;
    justify-content: center;
    list-style-type: none;
}
.page-item {
    border: 1px solid #ccc;
    cursor: pointer;
    padding: 0.6em;
}
.page-item > a {
    display: inline-block;
}

li.page-item.active {
    background-color: #2766cc;
}

li.page-item.active > a {
    color: white;
}

.text-center {
    text-align: center;
}

.sample input {
    display: none;
}
.sample label {
    display: block;
    float: left;
    cursor: pointer;
    width: 80px;
    margin: 0;
    padding: 11px 5px;
    border-right: 1px solid #abb2b7;
    background: #bdc3c7;
    color: #555e64;
    font-size: 14px;
    text-align: center;
    line-height: 1;
    transition: 0.2s;
}
.sample label:first-of-type {
    border-radius: 3px 0 0 3px;
}
.sample label:last-of-type {
    border-right: 0px;
    border-radius: 0 3px 3px 0;
}
.sample input[type="radio"]:checked + label {
    background-color: #a1b91d;
    color: #fff;
}
</style>
