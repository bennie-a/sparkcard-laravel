
<script setup>
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/msg/MessageArea.vue";
import ModalButton from "../component/modal/ModalButton.vue";
import axios from "axios";
import Loading from "vue-loading-overlay";
import PromoDropdown from "../component/PromoDropdown.vue";
import ColorDropdown from "../component/selection/ColorDropdown.vue";
import { ref } from "vue";
import { useRoute } from "vue-router";
import { ja } from "vuetify/locale";

const route = useRoute();
const color = ref("");
const setname = ref(route.query.setname);
const attr = ref(route.query.attr);
const  language = ref("ja");

const number = ref("");
const isLoading = ref(false);

const name = ref("");
const en_name = ref("");

const foiltype = ref([]);
const multiverse_id = ref("");
const image_url = ref("");

const search = async function () {
    isLoading.value = true;
    // this.$store.dispatch("message/clear");
    // this.$store.dispatch("clearMessage");
    const query = {
        params: {
            setcode: attr.value,
            number: number.value,
            language: language.value,
        },
    };
    await axios
        .get("/api/scryfall", query)
        .then((response) => {
            let data = response.data;
            name.value = data["name"];
            en_name.value = data["en_name"];
            multiverse_id.value = data["multiverseId"];
            color.value = data["color"];
            image_url.value = data["image_url"];
            foiltype.value = data["foiltype"];
        })
        .catch((e) => {
            console.log(e);
            if (e.response.status != 200) {
                console.error(e.response);
                // this.$store.dispatch(
                //     "message/error",
                //     e.response.data.detail
                // );
            }
        })
        .finally(() => {
            isLoading.value = false;
        });
}

// export default {
//     components: {
//         "message-area": MessageArea,
//         ModalButton: ModalButton,
//         loading: Loading,
//         promo:PromoDropdown
//     },
//     data() {
//         return {
//             setname: this.$route.query.setname,
//             attr: this.$route.query.attr,
//             name: "",
//             en_name: "",
//             isFoil: false,
//             promotype_id: 1,
//             number: "",
//             multiverse_id: "",
//             color: "",
//             imageurl: "",
//             language: "ja",
//             isLoading: false,
//             foiltype:[]
//         };
//     },
//     methods: {
//         store: function () {
//             const task = new AxiosTask(this.$store);
//             let json = {
//                 setCode: this.attr,
//                 name: this.name,
//                 isFoil: this.isFoil,
//                 promotype: this.promotype,
//                 multiverseId: this.multiverse_id,
//                 en_name: this.en_name,
//                 color: this.color,
//                 number: this.number,
//                 is_skip: false,
//                 image_url: this.imageurl,
//                 foiltype: ["通常版", "Foil"],
//                 promotype_id:this.promotype_id,
//             };
//             const success = function (response, store) {
//                 // this.back();
//                 console.log(response.status);
//                 store.dispatch("setSuccessMessage", `登録しました！`);
//             };
//             const fail = function () {};
//             task.post("/database/card", json, success);
//         },
//     },
// };
</script>
<template>
    <section>
        <v-chip label class="font-weight-bold">{{ setname }}[{{ attr }}]</v-chip>
        <v-form rounded class="pa-4 form_sheet w-75">
                <v-row gap="10">
                <v-col cols="3">
                    <v-radio-group v-model="language" inline>
                        <v-radio label="日本語" value="ja" color="teal-lighten-1"></v-radio>
                        <v-radio label="英語" value="en" color="teal-lighten-1"></v-radio>
                        </v-radio-group>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="カード番号" v-model="number"
                    prepend-inner-icon="mdi-numeric"> </v-text-field>
                </v-col>
                <v-col cols="2" class="text-right">
                    <v-btn @click="search" color="teal-lighten-1">検索する</v-btn>
                </v-col>
            </v-row>
        </v-form>
    </section>
    <section class="mt-8">
        <article>
            <v-row>
                <v-col col="6">
                    <v-row>
                        <v-col>
                            <v-text-field label="カード名(JP)" v-model="name"></v-text-field>
                        </v-col>
                        <v-col>
                            <v-text-field label="カード名(EN)" v-model="en_name"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="5">
                            <ColorDropdown v-model="color"></ColorDropdown>
                        </v-col>
                        <v-col>
                            <PromoDropdown v-model:name="promotype_id" v-model:setcode="attr"></PromoDropdown>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="4">
                            <div class="text-title-medium">仕上げ</div>
                           <div v-if="foiltype.length != 0">
                                <v-chip v-for="f in foiltype" :key="f"  class="mr-4" label>{{f}}</v-chip>
                        </div>
                        </v-col>
                        <v-col>
                            <div class="text-title-medium">Multiverse ID</div>
                           <div>{{ multiverse_id }}</div>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col>
                    <v-img :src="image_url" class="w-50"></v-img>
                </v-col>
            </v-row>
        </article>
        <article class="text-center mt-6">
            <ModalButton @action="store"
            ><span class="mdi mdi-check-bold"></span>
            登録する
        </ModalButton>
        <loading
            :active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
        ></loading>
        </article>
    </section>
    <section class="ui grid">
        <div class="eight wide column">
            <div class="ui form">
            </div>
        </div>
        <div class="four wide column">
            <img :src="imageurl" :alt="name" />
        </div>
    </section>
    <section>
    </section>
</template>
