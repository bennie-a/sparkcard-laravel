<template>
    <v-chip variant="flat" label density="compact" size="small" :class="foilClass(foiltype)">
        <v-icon icon="mdi-creation" class="mr-1"></v-icon>
        {{ foiltype }}
        </v-chip>
</template>
<script>
export default {
    props: {
        isFoil: { type: Boolean },
        foiltype: { type: String, default: "Foil" },
    },
    computed: {
        foilClass: function () {
            return (name) => {
                if (name == "Foil") {
                    return "bg-yellow-darken-2";
                }
                return "bg-pink-darken-2";
            };
        },
    },
};
</script>
<style>
.topaz {
    background-color: #e9bc00 !important;
}
.ruby {
    background-color: #c70067 !important;
    color: white !important;
}
</style>
