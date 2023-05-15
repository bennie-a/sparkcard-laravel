<template>
    <message-area></message-area>
    <div class="ui message">
        <p>注文用CSVファイルをアップロードしてください。</p>
    </div>
    <csv-upload @upload="orderUpload">アップロード</csv-upload
    ><label class="ml-1">{{ filename }}</label>
    <div class="mt-2">
        <button
            v-show="orders.length != 0"
            class="ui button basic teal"
            @click="copyPacking"
        >
            <i class="bi bi-clipboard-fill mr-half"></i>宛先をコピーする
        </button>
        <div v-if="isCopyed" class="ui left pointing blue label">
            コピーしました
        </div>
        <table class="ui table striped definition" v-for="o in orders">
            <tr>
                <td>注文番号</td>
                <td>{{ o.order_id }}</td>
            </tr>
            <tr>
                <td>商品名</td>
                <td>
                    <ul>
                        <li v-for="item in o.items">{{ item }}</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>宛先</td>
                <td class="address">
                    <span>{{ o.postcode }}</span>
                    <span>{{ o.address1 }}</span>
                    <span>{{ o.address2 }}</span>
                    <span>{{ o.name }}様</span>
                </td>
            </tr>
        </table>
    </div>
</template>
<script>
import Encoding, { orders } from "encoding-japanese";
import MessageArea from "../component/MessageArea.vue";
import CSVUpload from "../component/CSVUpload.vue";
export default {
    data() {
        return {
            isCopyed: false,
            filename: "",
            orders: [],
        };
    },
    methods: {
        // アップロードイベント
        orderUpload: function (file) {
            this.$papa.parse(file, {
                header: true,
                complete: function (results) {
                    this.filename = file.name;
                    let data = results.data;
                    let idKey = "order_id";
                    data.map((line, index) => {
                        let order = {};
                        let name = line["shipping_name"];
                        let beforeOrder = this.orders[index - 1];
                        if (index > 0 && name == beforeOrder["name"]) {
                            beforeOrder["items"].push(line["product_name"]);
                        } else {
                            order["items"] = [line["product_name"]];
                            order[idKey] = line[idKey];
                            order[
                                "postcode"
                            ] = `〒${line["shipping_postal_code"]}`;
                            order[
                                "address1"
                            ] = ` ${line["shipping_state"]}${line["shipping_city"]}${line["shipping_address_1"]}`;
                            order["address2"] = line["shipping_address_2"];
                            order["name"] = name;
                            this.orders.push(order);
                        }
                    });
                }.bind(this),
            });
        },
        // 商品と宛先をコピーする
        copyPacking: function () {
            let copytext = "";
            this.orders.forEach(function (order) {
                let items = order.items.join(",");
                copytext += `${items}\n${order.postcode}\n${order.address1}\n`;
                if (order.address2 != "") {
                    copytext += `${order.address2}\n`;
                }
                copytext += `${order.name}様\n`;
            });
            navigator.clipboard.writeText(copytext);
            this.isCopyed = true;
            setTimeout(() => {
                this.isCopyed = false;
            }, 2000);
        },
    },
    components: { "csv-upload": CSVUpload, "message-area": MessageArea },
};
</script>
<style>
td.address > span {
    display: block;
}
</style>
