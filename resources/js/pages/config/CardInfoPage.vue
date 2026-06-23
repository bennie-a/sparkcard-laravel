<script setup>
import { AxiosTask } from "../../component/AxiosTask";
import ModalButton from "../component/modal/ModalButton.vue";
import axios from "axios";
import Loading from "vue-loading-overlay";
import PromoDropdown from "../component/selection/PromoDropdown.vue";
import ColorDropdown from "../component/selection/ColorDropdown.vue";
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { MsgStore } from "../component/msg/MsgStore.js";
import RequiredLabel from "../component/label/RequiredLabel.vue";
import { useForm, useField } from 'vee-validate';
import * as yup from 'yup';

const route = useRoute();
const router = useRouter();
const color = ref("");
const setname = ref(route.query.setname);
const attr = ref(route.query.attr);
const  language = ref("ja");

const isLoading = ref(false);
const msgStore = MsgStore();

const name = ref("");
const en_name = ref("");
const foiltype = ref([]);
const multiverse_id = ref("");
const image_url = ref("");
const promotype_id = ref(null);

let numberLabel = 'カード番号';

const schema = yup.object({
    number:yup.string().label(numberLabel).required()
});

const {handleSubmit} = useForm({
    validationSchema:schema
});

const {value:number, errorMessage:numMsg} = useField('number');
const isDisplay = ref(false);
const search = handleSubmit(
    async function () {
        isLoading.value = true;
        msgStore.clear();
        isDisplay.value = false;
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
                console.log(data);
                name.value = data["name"];
                en_name.value = data["en_name"];
                multiverse_id.value = data["multiverseId"];
                color.value = data["color"];
                image_url.value = data["image_url"];
                foiltype.value = data["foiltype"];

                isDisplay.value = true;

            })
            .catch((e) => {
                msgStore.error(e.response.data.detail);
            })
            .finally(() => {
                isLoading.value = false;
            });
        }
    );

const store = () => {
    isLoading.value = true;
    const task = new AxiosTask();
    let json = {
        setCode: attr.value,
        name: name.value,
        multiverseId: multiverse_id.value,
        en_name: en_name.value,
        color: color.value,
        number: number.value,
        is_skip: false,
        image_url: image_url.value,
        foiltype: foiltype.value,
    };
    task.post("/database/card", json);
    isLoading.value = false;
}

const toList = () => {
    router.push({
        name:'Ex',
        query:{'attr':attr.value}
    });
}

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
                    <v-text-field :placeholder="numberLabel" v-model="number" validate-on="input"
                    prepend-inner-icon="mdi-numeric" type="number" min="1" :error-messages="numMsg">
                        <template v-slot:label>
                            <RequiredLabel :text="numberLabel" required></RequiredLabel>
                        </template>
                </v-text-field>
                </v-col>
                <v-col cols="2" class="text-right">
                    <v-btn @click="search" color="teal-lighten-1">検索する</v-btn>
                </v-col>
            </v-row>
        </v-form>
    </section>
    <section class="mt-8" v-if="isDisplay">
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
                            <PromoDropdown v-model:id="promotype_id" v-model:setcode="attr"></PromoDropdown>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="4">
                            <div class="text-title-medium">仕上げ</div>
                           <div v-if="foiltype.length != 0">
                                <v-chip v-for="f in foiltype" :key="f"  class="mr-4" label>{{f}}</v-chip>
                        </div>
                        </v-col>
                    </v-row>
                    <v-row class="mt-10">
                        <v-col class="text-center">
                            <v-btn  variant="outlined" color="grey-darken-1" class="mr-4" @click="toList">
                                <v-icon icon="mdi-chevron-double-left" start></v-icon>一覧画面に戻る
                            </v-btn>
                            <ModalButton @action="store">
                            登録する
                        </ModalButton>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col>
                    <v-img :src="image_url" class="w-50"></v-img>
                </v-col>
            </v-row>
        </article>
    </section>
    <section>
        <loading
            :active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
        ></loading>
    </section>
</template>
