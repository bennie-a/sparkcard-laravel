<template>
        <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="four wide field">
                <label>購入者名</label>
                <input v-model="buyer" type="text">
            </div>
            <div class="three wide field">
                <label for="">発送日</label>
                <div>
                    <scdatepicker :selectedDate="shippingDate" @update="handleupdate"/>
                </div>
            </div>
        </div>
        <button
            id="search"
            :class="{ disabled: cardname == '' && setname == '' }"
                class="ui button teal"
                @click="search"
            >
            検索
            </button>
    </article>
    <article class="mt-2">
        <h2 class="ui medium dividing header">
            件数：1件
        </h2>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="one wide">出荷ID</th>
                    <th class="two wide center aligned">販売ショップ</th>
                    <th class="two wide">注文番号</th>
                    <th class="three wide">購入者名</th>
                    <th class="">合計金額</th>
                    <th class="two wide">発送日</th>
                    <th class="one wide">商品数</th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1111</td>
                    <td class=" center aligned"><shop :orderId="orderId"/></td>
                    <td>order_QgyazqM4Tt5FWusfhjSNQ</td>
                    <td>三崎健太&nbsp;<button class="ui icon mini teal button"><i class="bi bi-clipboard-plus-fill"></i></button></td>
                    <td><i class="bi bi-currency-yen"></i>333</td>
                    <td>2024/02/04</td>
                    <td class="one wide center aligned">1点</td>
                    <td class="center aligned selectable">
                        <router-link to="/shipping/detail"><i class="bi bi-chevron-double-right"></i></router-link></td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination/>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
    </article>
</template>
<script>
import ShopTag from "../component/ShopTag.vue";
import ScDatePicker from "../component/ScDatePicker.vue";
export default {
    components: {
        scdatepicker:ScDatePicker,
        shop:ShopTag
    },
    data() {
        return {
            shippingDate : new Date(),
            orderId:"order_xgXgHv3ohAEHpkkwaFE8zF",
            buyer:''
        };
    },
    methods:{
        dateFormat: function (date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}/${month}/${day}`;
        },
        handleupdate:function(value) {
            this.shippingDate = value;
        }
    }
}
</script>
<style>
</style>