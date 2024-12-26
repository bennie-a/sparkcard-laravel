<script setup>
import { ref } from 'vue';
import { useRouter } from "vue-router";
import scdatepicker from "../component/SCDatePicker.vue";
import vendortag from "../component/tag/VendorTag.vue"

const itemname = ref("");
const date = ref("");
const arrivalDate = ref(new Date());

const router = useRouter();
const vendorId =3;
    // 詳細画面を表示する。
const toDssPage = (arrivalDate, vendorId) => {
    router.push({
        name: "ArrivalLogDss",
        params: { arrival_date: arrivalDate, vendor_id:vendorId},
    });
}
</script>
<template>
        <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="four wide field">
                <label>商品名(一部)</label>
                <input v-model="itemname" type="text">
            </div>
            <div class="three wide field">
                <label for="">入荷日</label>
                <div>
                    <scdatepicker v-model="arrivalDate"></scdatepicker>
                </div>
            </div>
        </div>
        <button
            id="search" class="ui button teal">
            検索
            </button>
    </article>
    <article class="mt-2">
        <h2 class="ui medium dividing header">
            件数：5件
        </h2>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="two wide center aligned">入荷日</th>
                    <th class="">取引先</th>
                    <th class="">商品名</th>
                    <th class="one side center aligned">原価合計</th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center aligned">2024/10/31</td>
                    <td><vendortag v-model="vendorId"></vendortag><span class="ml-half">晴れる屋トーナメントセンター大阪</span></td>
                    <td>【DSK】不浄なる者、ミケウス[JP]ほか<label class="ui label ml-half">40</label></td>
                    <td class=" center aligned"><i class="bi bi-currency-yen"></i>1600</td>
                    <td class="center aligned selectable">
                        <a @click="toDssPage('2024/10/31', 1)">
                            <i class="angle double right icon"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </article>
</template>