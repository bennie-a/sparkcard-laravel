<script setup>
    defineProps({
        text:{type:String, required:true}
    });
    const emit = defineEmits(['action']);

    const execute = () =>{
        emit('action');
    };
   </script>
<template>
    <v-btn color="teal-lighten-1" @click="execute">
        {{ text }}
        </v-btn>
</template>
