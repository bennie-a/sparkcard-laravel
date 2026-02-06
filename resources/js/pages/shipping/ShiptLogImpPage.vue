<script setup>

    import { reactive, ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";
    import pagination from "../component/ListPagination.vue";
    import Loading from "vue-loading-overlay";
    import axios from 'axios';
    import condition from "../component/tag/ConditionTag.vue";
    import pglist from "../component/PgList.vue";
    import cardlayout from "../component/CardLayout.vue";
import { has } from 'lodash';

    const router = useRouter();
    const result = reactive([]);
    const resultCount = ref(0);
    const isLoading = ref(false);
    const pglistRef = ref();
    const currentList = reactive([]);

    const error = reactive([]);
    const hasError = ref(false);
    const hasResult = ref(false);
    const upload = function() {

    };

    const current = (data) => {
        currentList.value = data.response;
    }

    const uploadFile = async(file) => {
        hasError.value = false;
        error.value = [];
        hasResult.value = false;
        isLoading.value = true;
        const formData = new FormData();
        formData.append('file', file);

        await axios.post('/api/shipping/parse', formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response) => {
                result.value = response.data;
                resultCount.value = result.value.length;
                hasResult.value = true;
                console.log(result.value);
            }).catch((e) => {
                hasError.value = true;
                error.value = e.response.data;
                console.log('Error:', error.value);
            }).finally(() => {
                isLoading.value = false;
            });
    };

</script>

<template>
    <div class="ui negative message" v-if="hasError">
        <div class="header">{{ error.value.detail }}</div>
        <ul class="list">
            <li v-for="row in error.value.rows" :key="row.row">
                {{ row.row }}行目：{{ row.msg }}
            </li>
        </ul>
    </div>
    <div id="upload_form" class="ui form grid segment">
        <div class="seven wide column">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </div>
    <div class="mt-3 ui grid">
        <div class="row">
            <div class="seven wide left floated column">
                <pglist ref="pglistRef" v-model:list="result.value" @loadPage="current"></pglist>
            </div>
            <div class="four wide right floated column ui right aligned" v-if="hasResult">
                <button class="ui teal button" @click="router.back()">インポート</button>
            </div>
        </div>
    </div>
    <div class="mt-1">
        <div class="ui padded segment" v-for="(r, index) in result.value" :key="index">
            <div>
               {{r.order_id}}<label class="ml-1 ui red  label">{{r.shipping_date}}発送</label>
            </div>
            <address id="buyer" class="ui secondary segment">
                <p>〒{{ r.zip_code }}</p>
                <p>{{ r.address }}</p>
                <p class="name">{{ r.buyer_name }}様</p>
            </address>
            <table class="ui striped table">
                <thead>
                    <tr>
                        <th class="one wide center aligned">在庫ID</th>
                        <th>カード情報</th>
                        <th class="two wide center aligned">状態</th>
                        <th class="two wide center aligned">枚数</th>
                        <th class="two wide center aligned">単価</th>
                        <th class="two wide center aligned">小計</th>
                        <th class="one wide center aligned">登録済み</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in r.items" :key="idx">
                        <td class="one wide center aligned">{{ item.stock.id }}</td>
                        <td><cardlayout v-model:card="item.stock.card" v-model:lang="item.stock.lang"/></td>
                        <td class="one wide center aligned"><condition :name="item.stock.condition"/></td>
                        <td class="center aligned">{{ item.shipment }}枚</td>
                        <td class="center aligned"><i class="bi bi-currency-yen"></i>{{ item.single_price }}</td>
                        <td class="center aligned"><i class="bi bi-currency-yen"></i>{{ item.product_price }}</td>
                        <td class="two wide center aligned">
                            <i class="check circle green big icon" v-if="item.isRegistered"></i>
                            <i class="close icon" v-if="!item.isRegistered"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
        <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />

</template>

<style scoped>
#upload_form {
    padding: 1rem;
}

#buyer > p {
    line-height: 0.6rem;
    color: #444;
}

#buyer > .name {
    font-weight: 700;
    font-size: 1.2rem;
    margin-top: 1rem;
}

</style>
