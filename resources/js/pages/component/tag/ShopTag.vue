<template>
     <v-chip  label variant="flat" color="red-lighten-1" v-if="orderId.startsWith('order_')">メルカリShops</v-chip>
     <v-chip label variant="flat" color="teal-lighten-1" v-else>BASEショップ</v-chip>
</template>
<script setup>
    defineProps({
        orderId: { type: String, required:true },
    });
</script>
