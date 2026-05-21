<script setup>
import { ref } from "vue";
    const lang = defineModel();
    const cat = ["JP", "EN", "IT", "CS", "CT"];
</script>
<template>
    <v-chip-group v-model="lang" mandatory column>
        <v-chip v-for="item in cat" :key="item" :value="item" class=""
        :variant="item === lang ? 'flat' : 'outlined'" color="teal-lighten-1" label>
            {{ item }}
        </v-chip>
    </v-chip-group>
</template>
<style scoped>
</style>
