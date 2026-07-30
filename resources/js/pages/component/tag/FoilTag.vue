<template>
    <v-chip density="compact" size="small" label variant="flat"
        v-if="isFoil" :color="foilClass(foiltype)">
        <v-icon icon="mdi-creation" class="mr-1"></v-icon>
        {{ foiltype }}
        </v-chip>
</template>
<script setup>
defineProps({
    isFoil: { type: Boolean },
    foiltype: { type: String, default: "Foil" },
});
const foilClass = (name) => {
    if (name == "Foil") {
        return "purple-darken-2";
    }
    return "pink-darken-2";
};
</script>
