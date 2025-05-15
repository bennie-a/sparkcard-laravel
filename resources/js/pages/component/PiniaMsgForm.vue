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
</template>
