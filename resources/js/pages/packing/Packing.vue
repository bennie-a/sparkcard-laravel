<template>
    <message-area></message-area>
    <div class="ui message">
        <p>注文用CSVファイルをアップロードしてください。</p>
    </div>
    <csv-upload @upload="orderUpload">アップロード</csv-upload
    ><label class="ml-1">{{ filename }}</label>
    <div class="mt-2">
        <table class="ui table striped">
            <thead>
                <tr>
                    <th>注文番号</th>
                    <th>商品名</th>
                    <th>住所</th>
                    <th>氏名</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="o in orders">
                    <td>{{ o.order_id }}</td>
                    <td>【MOM】救済の波濤[JP][白]</td>
                    <td>{{ o.address }}</td>
                    <td>{{ o.name }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import Encoding from "encoding-japanese";
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
                    data.map((line) => {
                        let order = {};
                        order["order_id"] = line["order_id"];
                        order[
                            "address"
                        ] = `〒${line["shipping_postal_code"]} ${line["shipping_state"]}${line["shipping_city"]}${line["shipping_address_1"]} ${line["shipping_address_2"]}`;
                        order["name"] = `${line["shipping_name"]}様`;
                        this.orders.push(order);
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
