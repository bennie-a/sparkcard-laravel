<script  setup>
import { onMounted, reactive, ref, watch } from "vue";
import axios from "axios";
import RequiredLabel from "../label/RequiredLabel.vue";
import { usePromoList } from "../../../composables/UsePromoList.js";
const id = defineModel("id");
const setcode = defineModel("setcode");

const { list, load, loading } = usePromoList();

watch(setcode, (value) => {
    load(value);
    }, {immediate:true}
);
</script>
<template>
    <v-alert  v-if="id && !list.some(item => item.id === id)"
  type="warning"
  density="compact">
    未登録プロモタイプ:No.{{id}}
</v-alert>
    <v-select v-model="id" :items="list" :loading="loading"
     item-value="id" item-title="name" label="プロモタイプ" v-else>
        <template v-slot:label>
            <required-label text="プロモタイプ" required></required-label>
        </template>
    </v-select>
</template>
