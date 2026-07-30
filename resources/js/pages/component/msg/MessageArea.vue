<script setup>
import { MsgStore } from './MsgStore';
const msgStore = MsgStore();

</script>
<template>
    <v-alert :type="msgStore.type"   variant="tonal"
    class="mb-6" closable @click:close="msgStore.clear()"  v-if="msgStore.visible">
        <ul v-if="msgStore.type === 'error'">
            <li v-for="err in msgStore.errMsgs" :key="err">
                {{ err }}
            </li>
        </ul>
        <p v-if="msgStore.type === 'success'" class="text-title-medium">
            {{ msgStore.message }}
        </p>
    </v-alert>
</template>
