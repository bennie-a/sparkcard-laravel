<script setup>
    import { AxiosTask } from "../../component/AxiosTask";
    import MessageArea from "../component/msg/MessageArea.vue";
    import ModalButton from "../component/modal/ModalButton.vue";
    import DateInput from "../component/date/DateInput.vue";
    import { ref } from "vue";

    const name = ref("");
    const attr = ref("");
    const releaseDate = ref(new Date());
    const format = ref("スタンダード");
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

    const store = () => {
        const task = new AxiosTask(this.$store);
                let json = {
                    name: name.value,
                    attr: attr.value,
                    block: block.value,
                    format: format.value,
                    release_date: releaseDate.value,
                };
                const success = function (response, store) {
                    // this.back();
                };
                task.post("/database/exp", json, success);
                this.$store.dispatch(
                    "setSuccessMessage",
                    `${this.name}を登録しました！`
                );
    };
// export default {
//     data() {
//         return {
//             name: "",
//             attr: "",
//             block: "サンダー・ジャンクション",
//             format: "スタンダード",
//             release_date: null,
//         };
//     },
//     methods: {
//         release_format: function (date) {
//             const day = date.getDate();
//             const month = date.getMonth() + 1;
//             const year = date.getFullYear();
//             return `${year}/${month}/${day}`;
//         },
//         back: function () {
//             this.$router.push("/config/expansion");
//         },
//         store: function () {
//             const task = new AxiosTask(this.$store);
//             let json = {
//                 name: this.name,
//                 attr: this.attr,
//                 block: this.block,
//                 format: this.format,
//                 release_date: this.release_date,
//             };
//             const success = function (response, store) {
//                 // this.back();
//             };
//             task.post("/database/exp", json, success);
//             this.$store.dispatch(
//                 "setSuccessMessage",
//                 `${this.name}を登録しました！`
//             );
//         },
//     },
//     components: {
//         "message-area": MessageArea,
//         Datepicker: Datepicker,
//         ModalButton: ModalButton,
//     },
// };
</script>
<template>
    <section>
        <v-form class="form_sheet rounded pa-4 w-50 mx-auto">
            <v-row class="text-center">
                <v-col cols="8">
                    <v-text-field label="名称" v-model="name"></v-text-field>
                </v-col>
                <v-col cols="4">
                    <v-text-field label="略称" v-model="attr"></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6">
                    <v-select v-model="format" :items="formatOptions" label="フォーマット">
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
