<script setup>
import MessageArea from "../component/msg/MessageArea.vue";
import Loading from "vue-loading-overlay";
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { MsgStore } from "../component/msg/MsgStore.js";

const router = useRouter();
const route = useRoute();
const isLoading = ref(false);
const keyword = ref(route.query.attr);
const result = ref([]);
const resultCount = ref(0);
const msgStore = MsgStore();

onMounted(() => {
    if (keyword.value) {
        search();
    }
});

// 登録画面に遷移する。
const show = () => {
    router.push("/config/expansion/post");
}

const search = async() => {
    try {
        isLoading.value = true;
        result.value = [];
        resultCount.value = 0;
        msgStore.clear();
        const response = await axios.get("/api/database/exp", {
            params: { query: keyword.value }
        });
        result.value = response.data;
        resultCount.value = result.value.length;
    }catch(e) {
        let data = e.response.data;
        msgStore.error(data.detail);
    } finally {
        isLoading.value = false;
    }
}

// 各カード登録画面に遷移する。
const toCardPage = (name, ex) => {
    router.push({
        name:name,
        query: {'setname':ex.name, 'attr':ex.attr },
    });
}

</script>
<template>
    <article>
        <v-form rounded class="form_sheet pa-4">
            <v-row>
                <v-col cols="2">
                    <v-text-field
                        v-model="keyword"
                        label="セット略称"
                        append-inner-icon="mdi-magnify"
                        @click:append-inner="search"
                        clearable>
                </v-text-field>
                </v-col>
                <v-col cols="10" class="text-right">
                    <v-btn @click="show" color="teal-lighten-1" variant="outlined">新しく登録する</v-btn>
                </v-col>
                </v-row>
        </v-form>
    </article>
    <article class="mt-10"  v-if="resultCount > 0">
        <h2 class="text-title-medium">件数：{{ resultCount }}件</h2>
        <v-table class="item_list mt-4 border-thin">
            <thead>
                <tr>
                    <th width="20%">ID</th>
                    <th class="">名称</th>
                    <th  width="8%" class="text-center">略称</th>
                    <th width="10%" class="text-center">発売日</th>
                    <th width="10%" class="text-center">カード件数</th>
                    <th width="15%" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ex in result" :key="ex.id">
                    <td>{{ ex.notion_id }}</td>
                    <td>{{ ex.name }}</td>
                    <td  class="text-center">{{ ex.attr }}</td>
                    <td  class="text-center">{{ ex.release_date }}</td>
                    <td v-if="ex.count != 0"  class="text-center" :class="ex.count !== 0 ? 'bg-white' : 'bg-deep-orange-lighten-4'">
                        {{ ex.count }}件
                    </td>
                    <td class="text-right">
                        <v-btn icon="mdi-plus-circle" color="teal-lighten-1" class="mr-3" variant="text" @click="toCardPage('PostCardInfo', ex)"></v-btn>
                        <v-btn icon="mdi-file-document-outline" color="teal-lighten-1" variant="text" @click="toCardPage('CardInfoCsvPage', ex)"></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>
    </article>
    <Loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
