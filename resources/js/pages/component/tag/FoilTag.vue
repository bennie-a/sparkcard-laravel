<template>
    <v-chip variant="flat" label density="compact" size="small"
        v-if="isFoil" :class="foilClass(foiltype)">
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
        return "bg-yellow-darken-2";
    }
    return "bg-pink-darken-2";
};
</script>
