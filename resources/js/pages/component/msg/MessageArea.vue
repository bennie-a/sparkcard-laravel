<script setup>
import { MsgStore } from './MsgStore';
const msgStore = MsgStore();

</script>
<template>
    <v-alert :type="msgStore.type"   variant="tonal"
    class="mb-6" closable @click:close="msgStore.clear()" v-if="msgStore.visible">
        {{ msgStore.message }}
    </v-alert>
</template>
