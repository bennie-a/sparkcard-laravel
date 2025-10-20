<script setup>

    import { reactive, ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";
    import pagination from "../component/ListPagination.vue";
    import Loading from "vue-loading-overlay";
    import axios from 'axios';
    import condition from "../component/tag/ConditionTag.vue";
    import pglist from "../component/PgList.vue";

    const router = useRouter();
    const result = reactive([]);
    const resultCount = ref(0);
    const isLoading = ref(false);
    const pglistRef = ref();
    const currentList = reactive([]);

    const upload = function() {
        
    };

    const current = (data) => {
        currentList.value = data.response;
    }

    const uploadFile = async(file) => {
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
                console.log(result.value);
            }).catch((error) => {
                console.error('Error:', error);
            }).finally(() => {
                isLoading.value = false;
            });
    };

</script>

<template>
    <div id="upload_form" class="ui form grid segment">
        <div class="seven wide column">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </div>
    <div class="mt-3 ui grid">
        <div class="row">
            <div class="four wide left floated column">
                <button class="ui teal button" @click="router.back()">インポート</button>
            </div>
            <div class="seven wide right floated column ui right aligned">
                <pglist ref="pglistRef" v-model:list="result.value" @loadPage="current"></pglist>
            </div>
        </div>
    </div>
    <div class="mt-1">
        <div class="ui padded segment" v-for="(r, index) in result.value" :key="index">
            <div>
               {{r.order_id}}<label class="ml-1 ui red  label">{{r.shipping_date}}発送</label>
            </div>
            <address id="buyer" class="ui secondary segment">
                <p>〒{{ r.zipcode }}</p>
                <p>{{ r.address }}</p>
                <p class="name">{{ r.buyer_name }}様</p>
            </address>
            <table class="ui striped table">
                <thead>
                    <tr>
                        <th class="one wide center aligned">在庫ID</th>
                        <th>カード情報</th>
                        <th class="one wide center aligned">状態</th>
                        <th class="one wide center aligned">枚数</th>
                        <th class="two wide center aligned">単価</th>
                        <th class="two wide center aligned">小計</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in r.items" :key="idx">
                        <td class="one wide center aligned">{{ item.id }}</td>
                        <td>{{ item.card.name }}</td>
                        <td class="one wide center aligned"><condition :name="item.condition"/></td>
                        <td class="center aligned">{{ item.quantity }}枚</td>
                        <td class="two wide center aligned"><i class="bi bi-currency-yen"></i>{{ item.single_price }}</td>
                        <td class="two wide center aligned"><i class="bi bi-currency-yen"></i>{{ item.subtotal_price }}</td>
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