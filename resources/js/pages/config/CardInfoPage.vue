<template>
    <section class="ui grids">
        <message-area></message-area>
        <router-link to="/config/expansion"
            ><i class="bi bi-arrow-left"></i>一覧に戻る</router-link
        >

        <div class="mt-1 content ui form">
            <div class="two fields">
                <div class="three wide field">
                    <label for="">セット名</label>
                    {{ setname }}({{ attr }})
                </div>
                <div class="require three wide field">
                    <label for="number">カード番号</label>
                    <div class="ui action input">
                        <input type="text" v-model="number" />
                        <button class="ui button" @click="search">
                            <i class="search icon"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="two fields">
                <div class="require four wide field">
                    <label for="cardName">カード名</label>
                    <input type="text" id="cardName" v-model="name" />
                </div>
                <div class="require four wide field">
                    <label for="enName">英名</label>
                    <input type="text" id="cardName" v-model="enname" />
                </div>
            </div>
            <div class="four fields">
                <div class="two wide field">
                    <label for="color">色</label>
                    <span>{{ color }}</span>
                </div>
                <div class="two wide field">
                    <label for="multiverse_id">Multiverse ID</label>
                    <span>{{ multiverse_id }}</span>
                </div>
                <div class="two wide field">
                    <label for="">通常 or Foil</label>
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="foil" v-model="isFoil" />
                        <label for="foil">Foil</label>
                    </div>
                </div>
                <div class="two wide field">
                    <label for="promotype">プロモタイプ</label>
                    <span
                        v-if="promotype != ''"
                        class="ui large orange label"
                        >{{ promotype }}</span
                    >
                    <span v-if="promotype == ''" class="ui large label"
                        >通常</span
                    >
                </div>
            </div>
            <ModalButton @action="store"
                ><i class="checkmark icon"></i>登録する</ModalButton
            >
        </div>
    </section>
</template>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/MessageArea.vue";
import ModalButton from "../component/ModalButton.vue";
import axios from "axios";

export default {
    data() {
        return {
            setname: this.$route.params.setname,
            attr: this.$route.params.attr,
            name: "",
            enname: "",
            isFoil: false,
            promotype: "",
            number: "",
            multiverse_id: "",
            color: "",
        };
    },
    methods: {
        search: async function () {
            this.$store.dispatch("message/clear");
            const task = new AxiosTask(this.$store);
            const query = {
                params: {
                    setcode: this.attr,
                    number: this.number,
                },
            };
            this.$store.dispatch("setLoad", false);
            await axios
                .get("/api/scryfall", query)
                .then((response) => {
                    let data = response.data;
                    this.name = data["name"];
                    this.enname = data["enname"];
                    this.multiverse_id = data["multiverse_id"];
                    this.color = data["color"];
                    this.promotype = data["promotype"];
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
                    this.$store.dispatch("setLoad", false);
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
                en_name: this.enname,
                color: this.color,
                number: this.number,
            };
            const success = function (response, store) {
                // this.back();
                console.log(response.status);
                store.dispatch("setSuccessMessage", `登録しました！`);
            };
            task.post("/database/card", json, success);
        },
    },
    components: {
        "message-area": MessageArea,
        ModalButton: ModalButton,
    },
};
</script>
<style></style>
