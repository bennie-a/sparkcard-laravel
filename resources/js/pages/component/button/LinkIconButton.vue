<script setup>
    const emit = defineEmits(['action']);

    const execute = () =>{
        emit('action');
    };
   </script>
<template>
    <v-btn variant="text" icon="mdi-chevron-double-right" color="teal-lighten-1" @click="execute"></v-btn>
</template>
