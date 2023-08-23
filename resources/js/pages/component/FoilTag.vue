<template>
    <label class="ui horizontal yellow label" v-if="isFoil"
        ><i class="star icon"></i>{{ foiltype }}</label
    >
</template>
<script>
export default {
    props: { isFoil: { type: Boolean }, foiltype: { type: String } },
};
</script>
