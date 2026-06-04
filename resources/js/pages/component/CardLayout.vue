<script setup>
import { defineProps } from 'vue';
import foiltag from "../component/tag/FoilTag.vue";
import ImageModal from "../component/modal/ImageModal.vue";

defineProps({
    card: { type: Object, required: true },
    lang: { type: String, required: true }
});

const showImage = (id) => {
    const selecterId = `#${id}`;
    $(selecterId).modal("show");

}
</script>
<template>
        <v-row gap="10" class="align-center">
            <v-col cols="1">
                <ImageModal :url="card.image_url"  :height="40"/>
            </v-col>
            <v-col>
                <div class="text-title-medium">
                    {{ card.name }}&#91;{{ lang }}&#93;
                    <span v-if="card.promotype.id != '1'" class="text-label-medium">&#8810;{{card.promotype.name}}&#8811;</span>
                    <foiltag :isFoil="card.foil.is_foil" :foiltype="card.foil.name"/>
                </div>
                 <span class="text-medium-emphasis">{{ card.exp.name }}&#91;{{ card.exp.attr }}&#93;&#35;{{ card.number }}</span>
            </v-col>
        </v-row>
</template>
<style scoped>
div.image img {
    width: fit-content;
    height: 100% !important;
    object-position: 50% 20%;
    object-fit: cover;
    cursor: pointer!important;
}
</style>
