<script setup>
    import { piniaMsgStore } from "@/stores/global/PiniaMsg";
    const piniaMsg = piniaMsgStore();

</script>
<template>
    <div class="ui positive message"  v-if="piniaMsg.hasSuccess()">
        <div class="header">
        {{ piniaMsg.success }}
        </div>
    </div>
    <div class="ui negative message" v-if="piniaMsg.hasError()">
        <div class="header">
            下の内容を確認してください。
        </div>
        <ul class="list">
            <li v-for="(e, index) in piniaMsg.error" :key="index">
                {{ e }}
            </li>
        </ul>
    </div>
</template>
