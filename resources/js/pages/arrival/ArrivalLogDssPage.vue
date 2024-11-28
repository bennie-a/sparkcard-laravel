<script setup>
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const arrival_date = route.params.arrival_date;
const vendor_id = route.params.vendor_id;

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
                    <td>霜の暴君、アイシングデス[JP]</td>
                    <td class="center aligned"><label class="ui horizonal label blue">NM</label></td>
                    <td class="center aligned">1枚</td>
                    <td class="center aligned"><i class="bi bi-currency-yen"></i>20</td>
                    <td class="center aligned selectable">
                        <a style="font-size: 1.2rem;"><i class="bi bi-pencil-square"></i></a>
                    </td>
                    <td class="center aligned selectable">
                        <a style="font-size: 1.2rem;"><i class="bi bi-trash3"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </article>
</template>