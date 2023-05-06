<template>
    <p>注文用CSVファイルをアップロードしてください。</p>
    <csv-upload @upload="orderUpload">アップロード</csv-upload>
    <table class="ui table striped">
        <thead>
            <tr>
                <th>注文番号</th>
                <th>商品名</th>
                <th>住所</th>
                <th>氏名</th>
            </tr>
        </thead>
        <tr>
            <td>order_GcaZ3wmfaF2sS7zzmJRQqX</td>
            <td>【MOM】救済の波濤[JP][白]</td>
            <td>〒852-8035 長崎県長崎市油木町41-160 井手口アパート202号室</td>
            <td>山見 啓太様</td>
        </tr>
    </table>
</template>
<script>
import CSVUpload from "../component/CSVUpload.vue";
export default {
    methods: {
        orderUpload: function (file) {
            console.log("OK");
        },
    },
    components: { "csv-upload": CSVUpload },
};
</script>
