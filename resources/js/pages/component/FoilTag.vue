<template>
    <label class="ui horizontal yellow label" v-if="isFoil"
        ><i class="star icon"></i>Foil</label
    >
</template>
<script>
export default {
    props: { isFoil: { type: Boolean } },
};
</script>
