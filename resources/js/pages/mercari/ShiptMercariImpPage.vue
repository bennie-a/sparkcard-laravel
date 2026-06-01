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
    import { usePagenate } from "@/pages/component/pagination/UsePaginate";
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
            "image_url": "https://cards.scryfall.io/large/front/0/6/061df0a2-1967-4ddd-84e3-3ecf3af98f6b.jpg?1593812878",
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
  ,
{
  "order_id": "order_D2rTxQm8Wk5NpV9HsYcE4F",
  "buyer_name": "山田 恒一",
  "zip_code": "812-0012",
  "address": "福岡県福岡市博多区博多駅中央街1-1",
  "shipping_fee": 185,
  "total_price": 1640,
  "coupon_discount_amount": 100,
  "items": [
    {
      "stock": {
        "id": 7421,
        "card": {
          "name": "思案",
          "exp": {
            "name": "マジック2012",
            "attr": "M12"
          },
          "number": "72",
          "image_url": "https://cards.scryfall.io/large/front/d/1/d1a43cf7-7aa2-41be-8bca-4a54460b2fa9.jpg?1562660726",
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
        "quantity": 4
      },
      "shipment": 2,
      "single_price": 180,
      "total_price": 720,
      "isRegistered": false
    },
    {
      "stock": {
        "id": 7422,
        "card": {
          "name": "定業",
          "exp": {
            "name": "マジック2011",
            "attr": "M11"
          },
          "number": "70",
          "image_url": "https://cards.scryfall.io/large/front/8/7/8784170b-8667-406c-9689-e6dcd36cf4e4.jpg?1562466830",
          "foil": {
            "is_foil": true,
            "name": "Foil"
          },
          "promotype": {
            "id": 1,
            "name": ""
          }
        },
        "condition": "EX+",
        "lang": "EN",
        "quantity": 1
      },
      "shipment": 1,
      "single_price": 835,
      "total_price": 835,
      "isRegistered": true
    }
  ]
},
{
  "order_id": "order_E8kLpVn3Xc6QrT1MwZdB5G",
  "buyer_name": "小林 真由",
  "zip_code": "980-0021",
  "address": "宮城県仙台市青葉区中央1-10-10",
  "shipping_fee": 120,
  "total_price": 920,
  "coupon_discount_amount": 0,
  "items": [
    {
      "stock": {
        "id": 8534,
        "card": {
          "name": "熊野と渇苛斬の対峙",
          "exp": {
            "name": "神河：輝ける世界",
            "attr": "NEO"
          },
          "number": "152",
          "image_url": "https://cards.scryfall.io/large/front/f/7/f7d02cb0-3d31-49c2-a95b-98f5b6e27c5c.jpg",
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
        "lang": "JP",
        "quantity": 2
      },
      "shipment": 1,
      "single_price": 240,
      "total_price": 480,
      "isRegistered": false
    },
    {
      "stock": {
        "id": 8535,
        "card": {
          "name": "ショック",
          "exp": {
            "name": "基本セット2021",
            "attr": "M21"
          },
          "number": "159",
          "image_url": "https://cards.scryfall.io/large/front/5/9/59fa8e8d-bcb8-47bf-b71a-df11c8d0f2c9.jpg?1641847379",
          "foil": {
            "is_foil": false,
            "name": ""
          },
          "promotype": {
            "id": 1,
            "name": ""
          }
        },
        "condition": "EX",
        "lang": "EN",
        "quantity": 4
      },
      "shipment": 2,
      "single_price": 80,
      "total_price": 320,
      "isRegistered": false
    }
  ]
},
{
  "order_id": "order_F5yMnKq1Zv8HtR4LpXsC7D",
  "buyer_name": "井上 恒一",
  "zip_code": "060-0005",
  "address": "北海道札幌市中央区北五条西2-5",
  "shipping_fee": 230,
  "total_price": 3180,
  "coupon_discount_amount": 200,
  "items": [
    {
      "stock": {
        "id": 9648,
        "card": {
          "name": "否定の力",
          "exp": {
            "name": "モダンホライゾン",
            "attr": "MH1"
          },
          "number": "52",
          "image_url": "https://cards.scryfall.io/large/front/6/4/64263968-0ebb-4cb7-8e4d-2c397b591457.jpg?1645877524",
          "foil": {
            "is_foil": false,
            "name": ""
          },
          "promotype": {
            "id": 1,
            "name": ""
          }
        },
        "condition": "EX+",
        "lang": "JP",
        "quantity": 1
      },
      "shipment": 1,
      "single_price": 2480,
      "total_price": 2480,
      "isRegistered": true
    },
    {
      "stock": {
        "id": 9649,
        "card": {
          "name": "ミシュラのガラクタ",
          "exp": {
            "name": "ダブルマスターズ",
            "attr": "2XM"
          },
          "number": "274",
          "image_url": "https://cards.scryfall.io/large/front/4/5/45bbbf9b-8fee-4c32-a513-02dac6ac8a39.jpg?1669300401",
          "foil": {
            "is_foil": true,
            "name": "Foil"
          },
          "promotype": {
            "id": 1,
            "name": ""
          }
        },
        "condition": "NM",
        "lang": "EN",
        "quantity": 1
      },
      "shipment": 1,
      "single_price": 670,
      "total_price": 670,
      "isRegistered": false
    }
  ]
}
]);

    const {
        page, pageCount, paginatedList, resetPage
    } = usePagenate(currentList, 4);



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
        resetPage();
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
            <v-sheet class="pa-4 mt-4" border rounded v-for="r in paginatedList" :key="r.order_id">
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
        <ListPagination v-model="page" :length="pageCount"></ListPagination> <!-- ページネーションコンポーネント -->
    </section>
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
