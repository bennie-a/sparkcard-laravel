<template>
    <article>
        <div class="ui  grid">
            <div class="two wide column">
                <h2 class="ui medium header">販売ショップ</h2>
                <shop :orderId="orderId"/>
            </div>
            <div class="four wide column">
                <h2 class="ui medium header">注文番号</h2>
                <p>order_QgyazqM4Tt5FWusfhjSNQ</p>
            </div>
            <div class="two wide column">
                <h2 class="ui medium header">発送日</h2>
                <p>2024/7/15</p>
            </div>
            <div class="four wide column">
                    <h2 class="ui medium header">購入者情報</h2>
                    <address>
                        <p>〒490-1400<br>愛知県海部郡飛島村大字飛島新田字竹之郷ヨタレ南ノ割979-3</p>
                        <p>小林 真理子</p>
                    </address>
                    <button class="ui teal basic tiny button" id="copy" @click="copyAddress">
                        <i class="bi bi-clipboard-fill mr-half"></i>コピー
                    </button>
                    <div v-if="isCopied" class="ui left pointing teal label">コピーしました</div>
            </div>
        </div>
        <h2 class="ui medium header">商品一覧</h2>
        <table class="ui table stripe">
            <thead>
                <th class="one wide">在庫ID</th>
                <th>カード</th>
                <th class="center aligned">言語</th>
                <th class="center aligned">状態</th>
                <th class="center aligned">枚数</th>
                <th class="center aligned">単価</th>
                <th class="center aligned">小計</th>
            </thead>
            <tr>
                <td>2330</td>
                <td>
                    <h4 class="ui image header">
                        <img src="https://cards.scryfall.io/png/front/1/1/11fbe52f-febd-49fc-8391-28d3efe9c3eb.png?1712356193" class="ui mini rounded image">
                        <div class="content">
                            収集家の檻
                            <div class="sub header">「サンダー・ジャンクションの無法者」_ビッグスコア</div>
                        </div>
                    </h4></td>
                <td class="center aligned">JP</td>
                <td class="center aligned"><label class="ui blue label">NM</label></td>
                <td class="center aligned">1枚</td>
                <td class="center aligned"><i class="bi bi-currency-yen"></i>560</td>
                <td class="center aligned"><i class="bi bi-currency-yen"></i>560</td>
            </tr>
        </table>
        <div class="text-center">
            <button class="ui gray basic button" @click="toList">一覧に戻る</button>
        </div>
    </article>
</template>
<script>
import ShopTag from '../component/ShopTag.vue';
export default {
    components:{
        shop:ShopTag
    },
    data(){
        return {
            orderId:"m_xgXgHv3ohAEHpkkwaFE8zF",
            isCopied:false
        };
    },
    methods:{
        toList:function() {
            this.$router.push("/shipping");
        },
         // 商品と宛先をコピーする
        copyAddress: function () {
            let copytext = "";
            // let items = order.items.join(",");
            //     copytext += `${items}\n${order.postcode}\n${order.address1}\n`;
            //     if (order.address2 != "") {
            //         copytext += `${order.address2}\n`;
            //     }
            //     copytext += `${order.name}様\n`;
            // });
            // navigator.clipboard.writeText(copytext);
            this.isCopied = true;
            setTimeout(() => {
                this.isCopied = false;
            }, 2000);
        }
    }
}
</script>
<style>
address {
    font-style: normal;
}
address > p {
    margin-bottom: 0.5em!important;
}

#copy:hover {
    color: white!important;
    background: #00B2AA!important;
    border: 0!important;
}
</style>