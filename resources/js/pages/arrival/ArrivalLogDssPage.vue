<script setup>
import { useRoute, useRouter } from "vue-router";
import cardlayout from "../component/CardLayout.vue";
import pagination from "../component/ListPagination.vue";

const route = useRoute();
const router = useRouter();

const arrival_date = route.params.arrival_date;
const vendor_id = route.params.vendor_id;
const card = {
    id:5555,
    cardname:'霜の暴君、アイシングデス[JP]',
    foil:{isFoil:true, name:"Foil"},
    setname:"フォーゴトン・レルム探訪",
    image_url:'https://cards.scryfall.io/png/front/b/0/b009f231-6dd2-468d-8005-04715cb9df1d.png?1645529044'
};

// 入荷情報一覧ページに戻る
const toList = () => {
    router.push("/arrival");
}

</script>
<template>
    {{ arrival_date }}{{ vendor_id }}
    <article class="mt-2">
        <h2 class="ui header">入荷商品</h2>
        <h3 class="ui medium dividing header">
            件数：5件
        </h3>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="two wide center aligned">入荷ID</th>
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
                    <td class="center aligned">523567</td>
                    <td><cardlayout :card="card"></cardlayout></td>
                    <td class="center aligned"><label class="ui horizonal label blue">NM</label></td>
                    <td class="center aligned">1枚</td>
                    <td class="center aligned"><i class="bi bi-currency-yen"></i>20</td>
                    <td class="center aligned selectable">
                        <a><i class="edit icon"></i></a>
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