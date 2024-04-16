<template>
    <message-area></message-area>
    <router-link to="/config/expansion"
        >
<i class="bi bi-arrow-left"></i>一覧に戻る</router-link
    >
    <div class="mt-1 ui form segment">
        <div class="two fields">
            <div class="three wide field">
                <label for="">セット名</label>
                {{ setname }}({{ attr }})
            </div>
            <div class="three wide field">
                <label for="">言語</label>
                <div class="inline fields">
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input
                                id="ja"
                                type="radio"
                                name="frequency"
                                v-model="language"
                                value="ja"
                            />
                            <label for="ja">日本語</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input
                                type="radio"
                                name="frequency"
                                v-model="language"
                                value="en"
                                id="en"
                            />
                            <label for="en">英語</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="three wide field">
                <label for="number">カード番号</label>
                <div class="ui action input">
                    <input
                        type="text"
                        v-model="number"
                        class="two wide columns"
                    />
                    <button class="ui button" @click="search">
                        <i class="search icon"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <section class="ui grid">
        <div class="eight wide column">
            <div class="ui form">
                <div class="two fields">
                    <div class="require eight wide field">
                        <label for="cardName">カード名</label>
                        <input type="text" id="cardName" v-model="name" />
                    </div>
                    <div class="require eight wide field">
                        <label for="enName">英名</label>
                        <input type="text" id="cardName" v-model="en_name" />
                    </div>
                </div>
                <div class="three fields">
                    <div class="eight wide field">
                        <label for="color">色</label>
                        <select v-model="color" class="ui dropdown">
                            <option value="W">白</option>
                            <option value="U">青</option>
                            <option value="R">赤</option>
                            <option value="B">黒</option>
                            <option value="G">緑</option>
                            <option value="M">多色</option>
                            <option value="A">アーティファクト</option>
                            <option value="Land">土地</option>
                        </select>
                    </div>
                    <div class="eight wide field">
                        <label for="promotype">プロモタイプ</label>
                        <select class="ui dropdown" v-model="promotype">
                            <option value="">通常</option>
                            <option value="フルアート">フルアート</option>
                            <option value="プレリリース">プレリリース</option>
                            <option value="「手配書」ショーケース">「手配書」ショーケース</option>
                            <option value="トーナメント景品">
                                トーナメント景品
                            </option>
                            <option value="プロモカード">プロモカード</option>
                            <option value="ボーダレス">ボーダレス</option>
                            <option value="Bring-a-Friend">
                                Bring-a-Friend
                            </option>
                            <option value="テキストレス・フルアート">
                                テキストレス・フルアート
                            </option>
                        </select>
                    </div>
                </div>
                <div class="two fields">
                    <div class="six wide field">
                        <label for="">仕上げ</label>
                        <span>{{ foiltype }}</span>
                    </div>
                    <div class="four wide field">
                        <label for="multiverse_id">Multiverse ID</label>
                        <span>{{ multiverse_id }}</span>
                    </div>
                </div>
                <ModalButton @action="store"
                    ><i class="checkmark icon"></i>
                    登録する
                </ModalButton>
            </div>
        </div>
        <div class="four wide column">
            <img :src="imageurl" :alt="name" />
        </div>
    </section>
    <section>
        <loading
            :active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
        ></loading>
    </section>
</template>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/MessageArea.vue";
import ModalButton from "../component/ModalButton.vue";
import axios from "axios";
import Loading from "vue-loading-overlay";

export default {
    components: {
        "message-area": MessageArea,
        ModalButton: ModalButton,
        loading: Loading,
    },
    data() {
        return {
            setname: this.$route.params.setname,
            attr: this.$route.params.attr,
            name: "",
            en_name: "",
            isFoil: false,
            promotype: "",
            number: "",
            multiverse_id: "",
            color: "",
            imageurl: "",
            language: "ja",
            isLoading: false,
            foiltype:[]
        };
    },
    methods: {
        search: async function () {
            this.isLoading = true;
            this.$store.dispatch("message/clear");
            const task = new AxiosTask(this.$store);
            const query = {
                params: {
                    setcode: this.attr,
                    number: this.number,
                    language: this.language,
                },
            };
            await axios
                .get("/api/scryfall", query)
                .then((response) => {
                    let data = response.data;
                    this.name = data["name"];
                    this.en_name = data["en_name"];
                    this.multiverse_id = data["multiverse_id"];
                    this.color = data["color"];
                    this.promotype = data["promotype"];
                    this.imageurl = data["imageurl"];
                    this.foiltype = data["foiltype"];
                })
                .catch((e) => {
                    console.error(e);
                    if (e.response.status != 200) {
                        this.$store.dispatch(
                            "message/error",
                            e.response.data.message
                        );
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        store: function () {
            const task = new AxiosTask(this.$store);
            let json = {
                setCode: this.attr,
                name: this.name,
                isFoil: this.isFoil,
                promotype: this.promotype,
                multiverseId: this.multiverse_id,
                en_name: this.en_name,
                color: this.color,
                number: this.number,
                isSkip: false,
                imageurl: this.imageurl,
                foiltype: ["通常版", "Foil"],
            };
            const success = function (response, store) {
                // this.back();
                console.log(response.status);
                store.dispatch("setSuccessMessage", `登録しました！`);
            };
            const fail = function () {};
            task.post("/database/card", json, success);
        },
    },
};
</script>
<style scoped>
img {
    width: 100%;
}
label {
    cursor: hand;
}
</style>
