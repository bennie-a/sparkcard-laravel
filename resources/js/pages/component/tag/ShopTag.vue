<template>
     <v-chip  label variant="tonal" color="red-lighten-1" v-if="orderId.startsWith('order_')"><strong>メルカリShops</strong></v-chip>
     <v-chip label variant="tonal" color="teal-lighten-1" v-else><strong>BASEショップ</strong></v-chip>
</template>
<script setup>
    defineProps({
        orderId: { type: String, required:true },
    });
</script>
