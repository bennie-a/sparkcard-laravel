<template>
    <message-area></message-area>
    <div class="ui message">
        <p>注文用CSVファイルをアップロードしてください。</p>
    </div>
    <csv-upload @upload="orderUpload">アップロード</csv-upload
    ><label class="ml-1">{{ filename }}</label>
    <div class="mt-2">
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
                <td>住所</td>
                <td>{{ o.address }}</td>
            </tr>
            <tr>
                <td>氏名</td>
                <td>{{ o.name }}様</td>
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
                                "address"
                            ] = `〒${line["shipping_postal_code"]} ${line["shipping_state"]}${line["shipping_city"]}${line["shipping_address_1"]} ${line["shipping_address_2"]}`;
                            order["name"] = name;
                            this.orders.push(order);
                        }
                    });
                    // console.log(line["order_id"]);
                    // this.contentMap[] = line["商品ID"];
                    // });
                }.bind(this),
            });
            console.log("OK");
        },
    },
    components: { "csv-upload": CSVUpload, "message-area": MessageArea },
};
</script>
