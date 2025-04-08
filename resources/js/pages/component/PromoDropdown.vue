<script  setup>
import { onMounted, reactive } from "vue";
import axios from "axios";
const name = defineModel("name");
const setcode = defineModel("setcode");
const list = reactive([]);
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
<select v-model="name" class="mr-1 ui dropdown">
    <option v-for="t in list.value" :key="t.id" :value="t.name">{{t.name }}</option>
</select>
</template>