<script setup>

    import { onMounted, reactive, ref } from 'vue';
    import FileUpload from "../component/FileUpload.vue";
    import Loading from "vue-loading-overlay";
    import axios from 'axios';
    import condition from "../component/tag/ConditionTag.vue";
    import cardlayout from "../component/CardLayout.vue";
    import ModalButton from '../component/modal/ModalButton.vue';
    import PiniaMsgForm from '../component/PiniaMsgForm.vue';
    import scdatepicker from "../component/SCDatePicker.vue";
    import ListPagination from "@/pages/component/pagination/ListPagination.vue";
    // import { usePagenate } from "./component/pagination/UsePaginate";
    // import {MsgStore} from "./component/msg/MsgStore";

    // const msgStore = MsgStore();

    const result = reactive([]);
    const resultCount = ref(0);
    const isLoading = ref(false);
    // const currentList = reactive([]);

    const error = reactive([]);
    const hasError = ref(false);
    const hasResult = ref(true);
    const shiptDate = ref(new Date());

    const currentList = ref([
  {
    "order_id": "order_A9xKpLm2Qw7RsT8VbNcD1E",
    "buyer_name": "佐藤 美咲",
    "zip_code": "150-0043",
    "address": "東京都渋谷区道玄坂2-24-1",
    "shipping_fee": 185,
    "total_price": 1280,
    "coupon_discount_amount": 0,
    "items": [
      {
        "stock": {
          "id": 4127,
          "card": {
            "name": "稲妻",
            "exp": {
              "name": "基本セット2010",
              "attr": "M10"
            },
            "number": "146",
            "image_url": "https://cards.scryfall.io/large/front/4/3/435589bb-27c6-4a6d-9d63-394d5092b9d8.jpg?1561978182",
            "foil": {
              "is_foil": false,
              "name": ""
            },
            "promotype": {
              "id": 1,
              "name": ""
            }
          },
          "condition": "NM",
          "lang": "EN",
          "quantity": 4
        },
        "shipment": 3,
        "single_price": 250,
        "total_price": 1000,
        "isRegistered": false
      },
      {
        "stock": {
          "id": 4128,
          "card": {
            "name": "対抗呪文",
            "exp": {
              "name": "ストリクスヘイヴン：ミスティカルアーカイブ",
              "attr": "STA"
            },
            "number": "15",
            "image_url": "https://cards.scryfall.io/large/front/8/1/8118282a-1473-4c0b-a283-1f58e0d0209a.jpg?1638111985",
            "foil": {
              "is_foil": true,
              "name": "Foil"
            },
            "promotype": {
              "id": 2,
              "name": "日本画ミスティカルアーカイブ"
            }
          },
          "condition": "EX+",
          "lang": "JP",
          "quantity": 1
        },
        "shipment": 1,
        "single_price": 280,
        "total_price": 280,
        "isRegistered": false
      }
    ]
  },
  {
    "order_id": "order_B4mNqXz8Lp2HrY7KdTfU6W",
    "buyer_name": "高橋 恒一",
    "zip_code": "530-0001",
    "address": "大阪府大阪市北区梅田3-1-1",
    "shipping_fee": 230,
    "total_price": 2130,
    "coupon_discount_amount": 50,
    "items": [
      {
        "stock": {
          "id": 5182,
          "card": {
            "name": "渦まく知識",
            "exp": {
              "name": "メルカディアン・マスクス",
              "attr": "MMQ"
            },
            "number": "61",
            "image_url": "https://cards.scryfall.io/large/front/9/f/9ff71d13-c4b7-4125-ab10-db4abbb7a074.jpg?1562382082",
            "foil": {
              "is_foil": false,
              "name": ""
            },
            "promotype": {
              "id": 1,
              "name": ""
            }
          },
          "condition": "NM-",
          "lang": "EN",
          "quantity": 3
        },
        "shipment": 2,
        "single_price": 350,
        "total_price": 1050,
        "isRegistered": false
      },
      {
        "stock": {
          "id": 5183,
          "card": {
            "name": "Sol Ring",
            "exp": {
              "name": "Commander Masters",
              "attr": "CMM"
            },
            "number": "396",
            "image_url": "https://cards.scryfall.io/large/front/a/6/a6d5e716-e939-4456-84e3-2ea22e8c7377.jpg?1734863703",
            "foil": {
              "is_foil": true,
              "name": "Foil"
            },
            "promotype": {
              "id": 1,
              "name": ""
            }
          },
          "condition": "EX",
          "lang": "CT",
          "quantity": 1
        },
        "shipment": 1,
        "single_price": 1080,
        "total_price": 1080,
        "coupon_discount_amount": 0,
        "isRegistered": true
      }
    ]
  },
  {
    "order_id": "order_C7vJdPw5Rq9XeK2MnLsA3B",
    "buyer_name": "中村 遥",
    "zip_code": "460-0008",
    "address": "京都府京都市東山区三条通南裏二筋目白川筋西入二丁目南側南木之元町123-4",
    "shipping_fee": 120,
    "total_price": 760,
    "coupon_discount_amount": 20,
    "items": [
      {
        "stock": {
          "id": 6391,
          "card": {
            "name": "Swords to Plowshares",
            "exp": {
              "name": "Double Masters 2022",
              "attr": "2X2"
            },
            "number": "280",
            "image_url": "https://cards.scryfall.io/large/front/0/6/06554274-78ee-4bfc-b203-b81cb4639427.jpg?1748705114",
            "foil": {
              "is_foil": false,
              "name": ""
            },
            "promotype": {
              "id": 1,
              "name": ""
            }
          },
          "condition": "NM",
          "lang": "JP",
          "quantity": 2
        },
        "shipment": 1,
        "single_price": 180,
        "total_price": 360,
        "isRegistered": false
      },
      {
        "stock": {
          "id": 6392,
          "card": {
            "name": "Path to Exile",
            "exp": {
              "name": "Modern Masters 2017",
              "attr": "MM3"
            },
            "number": "24",
            "image_url": "https://cards.scryfall.io/png/front/e/8/e8f5d7bb-0c9f-4d9a-9c55-6c0a9cbddf84.png",
            "foil": {
              "is_foil": false,
              "name": ""
            },
            "promotype": {
              "id": 1,
              "name": ""
            }
          },
          "condition": "NM-",
          "lang": "EN",
          "quantity": 1
        },
        "shipment": 1,
        "single_price": 200,
        "total_price": 200,
        "isRegistered": false
      },
      {
        "stock": {
          "id": 6393,
          "card": {
            "name": "選択",
            "exp": {
              "name": "ドミナリア",
              "attr": "DOM"
            },
            "number": "60",
            "image_url": "https://cards.scryfall.io/large/front/e/7/e7aa556e-1b4f-44a4-aadd-0d248d1c3071.jpg?1645614328",
            "foil": {
              "is_foil": false,
              "name": ""
            },
            "promotype": {
              "id": 1,
              "name": ""
            }
          },
          "condition": "PLD",
          "lang": "CT",
          "quantity": 1
        },
        "shipment": 1,
        "single_price": 80,
        "total_price": 80,
        "isRegistered": false
      }
    ]
  }
]);

    /**
     * インポート実行
     */
    const post = async function() {
        isLoading.value = true;
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
    <v-form rounded class="form_sheet pa-4">
        <div class="w-50">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </v-form>
    <section class="mt-10" v-if="hasResult">
        <h2 class="text-title-medium"
        >
           検索結果： {{ currentList.length }}件
        </h2>
        <v-form class="form_sheet pt-6 pl-6">
                <v-row>
                    <v-col cols="3">
                        <scdatepicker v-model:selectedDate="shiptDate" datelabel="発送日"></scdatepicker>
                    </v-col>
                    <v-col>
                        <ModalButton  @action="post()">インポート</ModalButton>
                    </v-col>
                </v-row>
        </v-form>
        <article>
            <v-sheet class="pa-4 mt-4" border rounded v-for="r in currentList" :key="r.order_id">
                <span class="text-body-small">{{ r.order_id }}</span>
                <v-row class="mt-3" gap="25">
                    <v-col cols="4">
                        <address id="buyer" class="pa-3 text-body-medium form_sheet">
                            <span>&#12306;{{ r.zip_code }}</span>
                            <p>{{ r.address }}</p>
                            <span class="text-title-medium font-weight-bold">{{ r.buyer_name }}様</span>
                        </address>
                    </v-col>
                    <v-col id="price" class="text-body-medium" cols="2">
                        <div  class="d-flex justify-space-between mb-1">
                            <span class="entry">送料</span>
                            <span>&yen;{{ r.shipping_fee }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-1">
                            <span class="entry">クーポン割引</span>
                                <span>
                                    &yen;{{ r.coupon_discount_amount }}
                                </span>
                        </div>
                           <v-divider  :thickness="1" class="border-opacity-50 my-2"  variant="dashed"></v-divider>
                        <div class="d-flex justify-space-between mb-1">
                            <span>合計金額</span>
                            <span>&yen;{{ r.total_price }}</span>
                        </div>
                    </v-col>
                </v-row>
                <v-table  class="mt-4 border-thin">
                <thead>
                    <tr class="bg-grey-lighten-3">
                        <th width="10%" class="text-left">在庫ID</th>
                        <th class="eight wide">カード情報</th>
                        <th  width="10%" class="text-center">状態</th>
                        <th  width="10%" class="text-center">枚数</th>
                        <th width="10%" class="text-center">単価</th>
                        <th width="10%" class="text-center">小計</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in r.items" :key="idx">
                        <td  class="text-left">{{ item.stock.id }}</td>
                        <td><cardlayout v-model:card="item.stock.card" v-model:lang="item.stock.lang"/></td>
                        <td class="text-center"><condition :name="item.stock.condition"/></td>
                        <td class="text-center">{{ item.shipment }}枚</td>
                        <td class="text-center">&yen;{{ item.single_price }}</td>
                        <td class="text-center">&yen;{{ item.total_price }}</td>
                    </tr>
                </tbody>
                </v-table>
            </v-sheet>
        </article>
    </section>
    <div class="mt-2" v-if="hasResult">
        <div class="ui grid segment" v-for="(r, index) in result.value" :key="index" style="padding:1rem">
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
                        <td class="center aligned">¥{{ item.single_price }}</td>
                        <td class="center aligned">¥{{ item.total_price }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="ui center aligned container">
        </div>
    </div>
    <loading
    :active="isLoading"
         :can-cancel="false" :is-full-page="true" />

</template>

<style scoped>

#price .entry::after {
    content: ":";
}

#price .list dd {
    text-align:  center;
    margin: 0;
    margin-left: 0.4rem;
}
</style>
