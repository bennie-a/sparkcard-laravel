<script setup>
import { useRoute, useRouter } from "vue-router";
import cardlayout from "../component/CardLayout.vue";
import pagination from "../component/ListPagination.vue";
import vendortag from "../component/tag/VendorTag.vue"
import {groupConditionStore} from "@/stores/arrival/GroupCondition";

const route = useRoute();
const router = useRouter();

const arrival_date = route.params.arrival_date;
const vendor_id = route.params.vendor_id;
const gcStore = groupConditionStore();

const card = {
    id:5555,
    cardname:'霜の暴君、アイシングデス[JP]',
    foil:{isFoil:true, name:"Foil"},
    setname:"フォーゴトン・レルム探訪",
    image_url:'https://cards.scryfall.io/png/front/b/0/b009f231-6dd2-468d-8005-04715cb9df1d.png?1645529044',
    vendor:{id:vendor_id, name:'オリジナルパック'},
};

// 入荷情報一覧ページに戻る
const toList = () => {
    router.push("/arrival");
}

// 入荷情報編集ページに遷移する
 const toEditPage = (arrival_id) => {
        router.push({
            name: "ArrivalLogEdit",
            params: { arrival_date:arrival_date, arrival_id: arrival_id},
        });
    }

</script>
<template>
    <article>
        <h2 class="ui medium header">入荷先</h2>
        <vendortag v-model="card.vendor"></vendortag>
        <span class="ml-half">晴れる屋トーナメントセンター大阪</span>
    </article>
    <article class="mt-2">
        <h2 class="ui medium header">入荷商品</h2>
        <h3 class="ui medium dividing header">
            件数：5件
        </h3>
        開始日：{{gcStore.startDate}}
        終了日: {{ gcStore.endDate }}
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="two wide center aligned">ID</th>
                    <th class="six wide">商品名</th>
                    <th class="two wide center aligned">状態</th>
                    <th class="center aligned">枚数</th>
                    <th class="center aligned">原価</th>
                    <th class="one wide"></th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center aligned">{{card.id}}</td>
                    <td><cardlayout :card="card"></cardlayout></td>
                    <td class="center aligned"><label class="ui horizonal label blue">NM</label></td>
                    <td class="center aligned">1枚</td>
                    <td class="center aligned"><i class="bi bi-currency-yen"></i>20</td>
                    <td class="center aligned selectable">
                        <a @click="toEditPage(card.id)"><i class="edit icon"></i></a>
                    </td>
                    <td class="center aligned selectable">
                        <a class="icon"><i class="trash alternate icon"></i></a>
                    </td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination></pagination>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
        <div class="text-center">
            <button class="ui gray basic button" @click="toList">一覧に戻る</button>
        </div>
    </article>
</template>