<script setup>
import imagemodal from '../component/ImageModal.vue';
import { defineProps } from 'vue';
import foiltag from '../component/FoilTag.vue';

const props = defineProps({
    card:{
        type:Object,
        required:true,
    }
});
const card = props.card;
</script>
<template>
        <h4 class="ui image header">
            <img :src="card.image_url" class="ui mini rounded image">
            <div class="content">
                {{ card.cardname }}<foiltag :isFoil="card.foil.isFoil" :name="card.foil.name"/>
                <div class="sub header">{{ card.setname }}</div>
            </div>
        </h4>
</template>