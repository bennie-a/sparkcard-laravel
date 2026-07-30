<script setup>
    import { AxiosTask } from "../../component/AxiosTask";
    import MessageArea from "../component/msg/MessageArea.vue";
    import ModalButton from "../component/modal/ModalButton.vue";
    import DateInput from "../component/date/DateInput.vue";
    import { ref } from "vue";
    import UseDateFormatter from "../../functions/UseDateFormatter.js";
    import RequiredLabel from "../component/label/RequiredLabel.vue";
    import { useForm, useField } from 'vee-validate';
    import * as yup from 'yup';
    import { MsgStore } from "../component/msg/MsgStore.js";

    const schema = yup.object({
        name:yup.string().label('名称').required(),
        attr:yup.string().label('略称').required().customAlpha(),
    });

    const {handleSubmit} = useForm({
        validationSchema:schema,
        initialValues:{
            name:'',
            attr:''
        }
    });

    const {value:name, errorMessage:nameMsg} = useField('name');
    const {value:attr, errorMessage:attrMsg} = useField('attr');
    const {toString} = UseDateFormatter();

    const releaseDate = ref(toString(new Date()));
    const format = ref("スタンダード")
    const block = ref("その他");


    const formatOptions = [
        "スタンダード",
        "パイオニア",
        "モダン",
        "レガシー",
        "統率者",
        "マスターピース",
        "その他",
    ];

    const msgStore = MsgStore();

    const store = handleSubmit(async () => {
        try {
            const task = new AxiosTask();
            let json = {
                name: name.value,
                attr: attr.value,
                block: block.value,
                format: format.value,
                release_date: releaseDate.value,
            };

            await task.post("/database/exp", json);

        } catch(e) {
            msgStore.error(e.message);
        }
    });
</script>
<template>
    <section>
        <v-form class="form_sheet rounded pa-4 w-50 mx-auto">
            <v-row>
                <v-col cols="8">
                    <v-text-field placeholder="名称" v-model="name" :error-messages="nameMsg">
                        <template v-slot:label>
                            <RequiredLabel text="名称" required></RequiredLabel>
                        </template>
                    </v-text-field>
                </v-col>
                <v-col cols="4">
                    <v-text-field placeholder="略称" v-model="attr" :errorMessages="attrMsg">
                        <template v-slot:label>
                            <RequiredLabel text="略称" required></RequiredLabel>
                        </template>
                    </v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6">
                    <v-select v-model="format" :items="formatOptions"  label="フォーマット">
                        <template v-slot:label>
                            <required-label text="フォーマット" required></required-label>
                        </template>
                    </v-select>
                </v-col>
                <v-col cols="6">
                    <date-input v-model:selectedDate="releaseDate" datelabel="発売日"></date-input>
                </v-col>
            </v-row>
            <div class="mt-6 text-center">
                <ModalButton @action="store">登録する</ModalButton>
            </div>
        </v-form>
    </section>
</template>
