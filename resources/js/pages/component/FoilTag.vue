<template>
    <label
        class="ui horizontal label"
        v-if="isFoil"
        :class="foilClass(foiltype)"
        ><i class="star icon"></i>{{ foiltype }}</label
    >
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
                    return "topaz";
                }
                return "ruby";
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
