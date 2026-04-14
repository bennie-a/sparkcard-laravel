<script setup>

    import { onMounted, reactive, ref } from 'vue';
    import FileUpload from "../component/FileUpload.vue";
    import Loading from "vue-loading-overlay";
    import axios from 'axios';
    import condition from "../component/tag/ConditionTag.vue";
    import pglist from "../component/PgList.vue";
    import cardlayout from "../component/CardLayout.vue";
    import ModalButton from '../component/ModalButton.vue';
    import PiniaMsgForm from '../component/PiniaMsgForm.vue';
    import { piniaMsgStore } from '@/stores/global/PiniaMsg.js';
    import scdatepicker from "../component/SCDatePicker.vue";


    const result = reactive([]);
    const resultCount = ref(0);
    const isLoading = ref(false);
    const currentList = reactive([]);

    const error = reactive([]);
    const hasError = ref(false);
    const hasResult = ref(false);
    const piniaMsg = piniaMsgStore();
    const shiptDate = ref(new Date());

    onMounted(() => {
        piniaMsg.reset();
    });

    /**
     * インポート実行
     */
    const post = async function() {
        isLoading.value = true;
        piniaMsg.reset();
        await Promise.all(result.value.map(async (r) => {
            const formatShiptDate = shiptDate.value.toLocaleDateString("ja-JP", {year: "numeric",month: "2-digit",
            day: "2-digit"})
            const json =
                {
                    order_id: r.order_id,
                    shipping_date: formatShiptDate,
                    buyer_name: r.buyer_name,
                    zip_code: r.zip_code,
                    address: r.address,
                    items: []
                };
            json.items = r.items.map((i) => {
                return {
                    id: i.stock.id,
                    shipment: i.shipment,
                    single_price: i.single_price,
                    total_price: i.total_price,
                    isRegistered: i.isRegistered
                };
            });

            await axios.post('/api/shipping', json)
                .then((response) => {
                    console.log('Imported:', response.data);
                }).catch((e) => {
                    console.log('Import Error:', e.response.data);
                });
            }));
        isLoading.value = false;
        piniaMsg.setSuccess('インポートしました。');
    };

    const current = (data) => {
        currentList.value = data.response;
    }

    const uploadFile = async(file) => {
        hasError.value = false;
        error.value = [];
        hasResult.value = false;
        isLoading.value = true;
        result.value = [];
        piniaMsg.reset();
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
    <PiniaMsgForm></PiniaMsgForm>
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
    <div class="mt-1 ui grid">
        <div class="ui form" v-if="hasResult">
                <div class="two fields">
                    <div class="nine wide column field">
                        <label>発送日</label>
                        <scdatepicker v-model="shiptDate"></scdatepicker>
                    </div>
                    <div class="seven wide column field">
                        <label style="visibility: hidden">インポートボタン</label>
                            <ModalButton  @action="post()">インポート</ModalButton>
                        </div>
                </div>
        </div>
    </div>
    <div class="mt-2" v-if="hasResult">
        <div class="ui grid segment" v-for="(r, index) in result.value" :key="index" style="padding:1rem">
            <div class="pl-0">
                {{r.order_id}}
            </div>
            <div class="ui three column row">
                <address id="buyer" class="column ui secondary segment">
                    <p>〒{{ r.zip_code }}</p>
                    <p >{{ r.address }}</p>
                    <p class="name">{{ r.buyer_name }}様</p>
                </address>
                <div class="column">
                    <dl id="price">
                        <div class="list" v-if="r.coupon_discount_amount != 0">
                            <dt>クーポン割引</dt>
                            <dd>
                                <i class="bi bi-dash"></i>
                                <i class="bi bi-currency-yen"></i>{{ r.coupon_discount_amount }}
                            </dd>
                        </div>
                        <div class="list">
                            <dt>送料</dt>
                            <dd><i class="bi bi-currency-yen"></i>{{ r.shipping_fee }}</dd>
                        </div>
                        <div class="list">
                            <dt>合計金額</dt>
                            <dd><i class="bi bi-currency-yen"></i>{{ r.total_price }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            <table class="ui striped table">
                <thead>
                    <tr>
                        <th class="one wide center aligned">在庫ID</th>
                        <th class="eight wide">カード情報</th>
                        <th class="center aligned">状態</th>
                        <th class="center aligned">枚数</th>
                        <th class="center aligned">単価</th>
                        <th class="center aligned">小計</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in r.items" :key="idx">
                        <td class="center aligned">{{ item.stock.id }}</td>
                        <td><cardlayout v-model:card="item.stock.card" v-model:lang="item.stock.lang"/></td>
                        <td class="one wide center aligned"><condition :name="item.stock.condition"/></td>
                        <td class="center aligned">{{ item.shipment }}枚</td>
                        <td class="center aligned"><i class="bi bi-currency-yen"></i>{{ item.single_price }}</td>
                        <td class="center aligned"><i class="bi bi-currency-yen"></i>{{ item.total_price }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="ui center aligned container">
            <pglist ref="pglistRef" v-model:list="result.value" @loadPage="current"></pglist>
        </div>
    </div>
    <loading
    :active="isLoading"
         :can-cancel="false" :is-full-page="true" />

</template>

<style scoped>

.ui.grid > table {
    padding: 0;
}

#upload_form {
    padding: 1rem;
}

#buyer > p {
    line-height: 1rem;
    color: #444;
}

#buyer > .name {
    font-weight: 700;
    font-size: 1.2rem;
    margin-top: 1rem;
}

#price .list {
  display: flex;
  margin-bottom: 0.5rem;
}
#price .list dt {
    font-weight: 700;
    text-align: right;
    width: 30%;
}

#price .list dt::after {
    content: ":";
}

#price .list dd {
    text-align:  center;
    margin: 0;
    margin-left: 0.4rem;
}
</style>
