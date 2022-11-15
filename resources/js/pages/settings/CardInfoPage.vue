<template>
    <message-area></message-area>
    <section class="mt-1">
        <file-upload @action="upload" type="json"></file-upload>
        {{ filename }}
    </section>
    <section class="wall mt-1">
        <now-loading></now-loading>
        <form class="ui large form">
            <h2 class="ui dividing header">登録内容</h2>
            <div class="inline field">
                <label>エキスパンション名：</label>{{ setCode }}
            </div>
            <div class="field">
                <table class="ui table striped six column">
                    <thead>
                        <tr>
                            <th class="one wide">カード番号</th>
                            <th class="four wide">カード名</th>
                            <th class="four wide">英名</th>
                            <th class="one wide">色</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="card in getCards">
                            <td>{{ card.number }}</td>
                            <td>
                                {{ card.name
                                }}<label v-if="card.promotype != ''"
                                    >≪{{ card.promotype }}≫</label
                                >
                            </td>
                            <td>{{ card.enname }}</td>
                            <td>{{ card.color }}</td>
                        </tr>
                    </tbody>
                    <tfoot v-if="this.$store.getters.cardsLength != 0">
                        <tr>
                            <th colspan="4">
                                <pagination></pagination>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </section>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";
import FileUpload from "../component/FileUpload.vue";
import MessageArea from "../component/MessageArea.vue";
import ListPagination from "../component/ListPagination.vue";

import axios from "axios";
export default {
    data() {
        return { filename: "ファイルを選択してください", setCode: "" };
    },
    computed: {
        getCards: function () {
            return this.$store.getters.sliceCard;
        },
    },
    methods: {
        upload: async function (file) {
            this.$store.dispatch("setLoad", true);
            this.filename = file.name;
            const config = {
                headers: {
                    "Content-Type": "application/json",
                },
            };
            await axios
                .post("/api/upload/card", file, config)
                .then((response) => {
                    if (response.status == 201) {
                        let item = response.data;
                        this.setCode = item.setCode;
                        this.$store.dispatch("setCard", item.cards);
                    }
                })
                .catch((e) => {
                    if (e.response.status == 422) {
                        const errors = e.response.data.errors;
                        let msgs = "<ul>";
                        for (let key in errors) {
                            console.log(key);
                            let array = errors[key];
                            array.forEach((msg) => {
                                msgs += `<li>${msg}</li>`;
                            });
                        }
                        msgs += "</ul>";
                        this.$store.dispatch("message/errorhtml", msgs);
                    }
                })
                .finally(() => {
                    this.$store.dispatch("setLoad", false);
                });
        },
    },
    components: {
        "file-upload": FileUpload,
        "message-area": MessageArea,
        "now-loading": NowLoading,
        pagination: ListPagination,
    },
};
</script>
<style>
.wall {
    background-color: white;
    padding: 1em;
}
</style>
