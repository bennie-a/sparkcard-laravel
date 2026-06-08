<template>
    <v-chip variant="flat" label density="compact" :color="colors[type].color + '-lighten-1'" >
        <v-icon :icon="colors[type].icon" class="mr-1"></v-icon>
        <strong>{{ type }}</strong>
        </v-chip>
</template>
<script setup>
defineProps({
    type: { type: String, required: true },
});

const colors = {
    W: { color: "yellow", icon:'mdi-weather-sunny'},
    B: { color: "black", icon:'mdi-skull-outline'},
    U: { color: "blue", icon:'mdi-water-outline'},
    R: { color: "red", icon:'mdi-fire'},
    G: { color: "green", icon:'mdi-pine-tree-variant-outline'},
    M: { color: "orange", icon:'mdi-multiplication-box'},
    L: { color: "grey", icon:'mdi-rhombus-outline'},
    A: { color: "blue-grey", icon:'mdi-gold  '},
    Art: { color: "pink", icon:'mdi-palette'},
    Land: { color: "brown", icon:'mdi-map-marker-radius-outline'},
};

const colorClass = (name) => {

    return colors[name].color + "";
};
// const foilClass = (name) => {
//     if (name == "Foil") {
//         return "bg-yellow-darken-2";
//     }
//     return "bg-pink-darken-2";
// };
</script>
