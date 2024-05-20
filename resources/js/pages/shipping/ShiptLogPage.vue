<template>
        <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="four wide field">
                <label>購入者名</label>
                <input v-model="cardname" type="text">
            </div>
            <div class="three wide field">
                <label for="">発送日</label>
                <div>
                    <datepicker
                        v-model="shippingDate"
                        input-class-name="dp_custom_input"
                        locale="jp"
                        :enable-time-picker="false"
                        :format="dateFormat"
                    />
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
                    <th class="two wide">ショップ名</th>
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
                    <td><label class="mercari ui label">メルカリShops</label></td>
                    <td>order_QgyazqM4Tt5FWusfhjSNQ</td>
                    <td>三崎健太&nbsp;<button class="ui icon mini teal button"><i class="bi bi-clipboard-plus-fill"></i></button></td>
                    <td><i class="bi bi-currency-yen"></i>333</td>
                    <td>2024/02/04</td>
                    <td class="one wide center aligned">1点</td>
                    <td class="center aligned selectable">
                        <router-link to="/shipping/detail"><i class="bi bi-chevron-double-right"></i></router-link></td>
                </tr>
                <!-- <tr
                    v-for="(s, index) in $store.getters.sliceCard"
                    :key="index"
                >
                    <td>{{ s.id }}</td>
                    <td>
                        <h4 class="ui image header">
                            <img
                                :src="s.image_url"
                                class="ui mini rounded image"
                                @click="$refs.modal[index].showImage(s.id)"
                            >
                            <div class="content">
                                {{ s.cardname
                                }}<foiltag :isFoil="s.isFoil"/>
                                <div class="sub header">{{ s.setname }}</div>
                            </div>
                            <image-modal
                                :url="s.image_url"
                                :id="s.id"
                                ref="modal"
                            />
                        </h4>
                    </td>
                    <td class="center aligned">{{ s.language }}</td>
                    <td class="center aligned">
                        <condition :name="s.condition"/>
                    </td>
                    <td class="center aligned">{{ s.quantity }}</td>
                    <td class="right aligned">{{ s.updated_at }}</td>
                </tr> -->
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
import Datepicker from "@vuepic/vue-datepicker";
export default {
    components: {
        datepicker:Datepicker,
    },
    data() {
        return {
            shippingDate : new Date()
        };
    },
    methods:{
        dateFormat: function (date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}/${month}/${day}`;
        },
    }
}
</script>
<style>
.mercari {
    color: #DB2828!important;
    background: rgb(255, 140, 65,0.2)!important;
}
</style>