<script  setup>
import { onMounted, reactive, ref } from "vue";
import axios from "axios";
const id = defineModel("id");
const setcode = defineModel("setcode");
const list = ref([]);
onMounted(async() => {
        // 特別版一覧を取得
        await axios
            .get('/api/promo/' + '?setcode=' + setcode.value)
            .then((response) => {
                list.value = response.data;
            })
            .catch((e) => {
                console.error(e);
            })
});
</script>
<template>
    <v-select v-model="id" :items="list"
     item-value="id" item-title="name" label="プロモタイプ">
    </v-select>
</template>
