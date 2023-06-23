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
            </div>
            <!-- <div class="two fields">
                <div class="require four wide field">
                    <label for="promotype">プロモタイプ</label>
                    <select id="promotype" v-model="promotype">
                        <option value="">通常</option>
                        <option value="ブースターファン">
                            ブースターファン
                        </option>
                        <option value="トーナメント景品">
                            トーナメント景品
                        </option>
                        <option value="ショーケース">ショーケース</option>
                    </select>
                </div>
                <div class="six wide field">
                    <label for="">通常 or Foil</label>
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="foil" v-model="isFoil" />
                        <label for="foil">Foil</label>
                    </div>
                </div>
            </div> -->
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

export default {
    data() {
        return {
            setname: this.$route.params.setname,
            attr: this.$route.params.attr,
            name: "",
            isFoil: false,
            promotype: "",
            number: "",
        };
    },
    methods: {
        search: async function () {
            const task = new AxiosTask(this.$store);
            const query = {
                params: {
                    setcode: this.attr,
                    number: this.number,
                },
            };

            const success = function (response, store, query) {
                let data = response.data;
                console.log(data["multiverse_id"]);
            };
            const fail = function (e, store, query) {
                console.error(e);
                if (e.response.status == 422) {
                    store.dispatch("message/error", e.response.data.message);
                }
            };
            await task.get("/scryfall", query, success, fail);
        },
        store: function () {
            const task = new AxiosTask(this.$store);
            let json = {
                setCode: this.attr,
                name: this.name,
                isFoil: this.isFoil,
                promotype: this.promotype,
                number: this.number,
            };
            const success = function (response, store) {
                // this.back();
            };
            task.post("database/card", json, success);
            this.$store.dispatch(
                "setSuccessMessage",
                `${this.name}を登録しました！`
            );
        },
    },
    components: {
        "message-area": MessageArea,
        ModalButton: ModalButton,
    },
};
</script>
<style></style>
