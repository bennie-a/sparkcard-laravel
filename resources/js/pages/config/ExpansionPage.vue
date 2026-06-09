<script setup>
import MessageArea from "../component/msg/MessageArea.vue";
import Loading from "vue-loading-overlay";
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const isLoading = ref(false);
const keyword = ref("");
const result = ref([]);
const resultCount = ref(0);

// 登録画面に遷移する。
const show = () => {
    router.push("/config/expansion/post");
}

const search = async() => {
    try {
        isLoading.value = true;
        result.value = [];
        const response = await axios.get("/api/database/exp", {
            params: { query: keyword.value }
        });
        result.value = response.data;
        resultCount.value = result.value.length;
    }catch(e) {
        console.error(e);
    } finally {
        isLoading.value = false;
    }
}

// export default {
//     data() {
//         return {
//             expansions: null,
//             keyword: null,
//         };
//     },
//     mounted: async function () {
//         this.$store.dispatch("message/clear");
//         this.$store.dispatch("expansion/clear");
//     },
//     methods: {
//         show: function () {
//             this.$router.push("/config/expansion/post");
//         },
//         // カード登録画面に遷移する。
//         toPostCardPage: function (setname, attr) {
//             this.$router.push({
//                 name: "PostCardInfo",
//                 params: { setname: setname, attr: attr },
//             });
//         },
//         // カードCSV登録画面に遷移する。
//         toCsvCardPage:function(attr) {
//             this.$router.push(
//                 {
//                     name:"CardInfoCsvPage",
//                     params:{attr:attr}
//                 }
//             );
//         },
//     },
//     components: {
//         "now-loading": NowLoading,
//         "message-area": MessageArea,
//     },
// };
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
    <article v-if="resultCount > 0">
                <table class="ui table striped six column">
            <thead>
                <tr>
                    <th class="">名称</th>
                    <th class="">略称</th>
                    <th>リリース日</th>
                    <th class="one wide center aligned">カード件数</th>
                    <th class="center aligned">カード登録</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ex in result" :key="ex.id">
                    <td>{{ ex.name }}</td>
                    <td class="one wide">{{ ex.attr }}</td>
                    <td class="one wide">{{ ex.release_date }}</td>
                    <td v-if="ex.count != 0" class="one wide positive center aligned">
                        {{ ex.count }}件
                    </td>
                    <td v-else class="negative center aligned">
                        {{ ex.count }}件
                    </td>
                    <td class="two wide right aligned">
                        <!-- <div class="ui buttons">
                            <button
                                class="ui button teal"
                                @click="toPostCardPage(ex.name, ex.attr)"
                            >
                            <v-icon icon="mdi-plus-circle"></v-icon>1件登録
                            </button>
                            <div class="or"></div>
                            <button class="ui button teal" @click="toCsvCardPage(ex.attr)">
                                <v-icon icon="mdi-file-document-outline"></v-icon>一括登録</button>
                        </div> -->
                    </td>
                </tr>
            </tbody>
        </table>
    </article>
    <Loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
