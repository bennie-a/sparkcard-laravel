<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";
let value = ref("");
onMounted(async () => {
    console.log("mounted");
    await axios
        .get("/api/wisdom")
        .then((response) => {
            console.log(response);
            value.value = response.data;
        })
        .catch((e) => {
            console.error(e);
        });
});
</script>

<template>
    <h1 class="display-6">カード登録</h1>
    <small class="text-muted">Wisdom Guildからカード情報を持ってきます</small>
    {{ value.name }}
    <table class="table table-striped table-bordered mt-3">
        <thead class="">
            <tr>
                <th scope="col">カード番号</th>
                <th scope="col">カード名</th>
                <th scope="col">英語名</th>
                <th scope="col">価格</th>
            </tr>
        </thead>
        <tr>
            <!-- <td>{{$card->getIndex()}}</td>
            <td>{{$card->getName()}}</td>
            <td>{{$card->getEnname()}}</td>
            <td>{{$card->getPrice()}}</td> -->
        </tr>
    </table>
</template>
