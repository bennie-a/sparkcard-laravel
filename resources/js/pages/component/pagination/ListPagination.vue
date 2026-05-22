    <script setup>
    const page = defineModel();
    defineProps({
        length:{type:Number, required:true}
    });
    </script>
<template>
    <v-pagination v-model="page" :length="length" rounded="circle" :total-visible="5"></v-pagination>
</template>
