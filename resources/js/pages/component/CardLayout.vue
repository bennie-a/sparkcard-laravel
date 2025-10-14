<script setup>
import { defineProps } from 'vue';
import foiltag from './tag/FoilTag.vue';

const card = defineModel("card", {
                type:Object,
                required:true,
            }
);
const lang = defineModel("lang", {
    type:String,
    required:true
});

const showImage = (id) => {
    const selecterId = `#${id}`;
    $(selecterId).modal("show");

}
</script>
<template>
        <h4 class="ui image header">
            <img :src="card.image_url" class="ui mini rounded image" @click="showImage(card.id)">
            <div class="content">
                {{ card.name }}&#91;{{ lang }}&#93;
                <div v-if="card.promotype.id != '1'">&#8810;{{card.promotype.name}}&#8811;
                    <span v-if="card.foil"><foiltag :isFoil="card.foil.is_foil" :name="card.foil.name"/></span>
                </div>
                <div class="sub header">{{ card.exp.name }}&#91;{{ card.exp.attr }}&#93;&#35;{{ card.number }}</div>
            </div>
            <div class="ui tiny modal" v-bind:id="card.id">
                <i class="close icon"></i>
                <div class="image content">
                    <img v-bind:src="card.image_url" class="image" />
                </div>
            </div>
        </h4>
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