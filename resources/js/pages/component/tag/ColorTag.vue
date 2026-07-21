<template>
    <v-chip variant="flat" label density="compact" :color="ColorMaster.find(type).color + '-lighten-1'" >
        <v-icon :icon="ColorMaster.find(type).icon" class="mr-1"></v-icon>
        <strong>{{ type }}</strong>
        </v-chip>
</template>
<script setup>
import { ColorMaster } from '../const/ColorMaster';
defineProps({
    type: { type: String, required: true },
});
</script>
