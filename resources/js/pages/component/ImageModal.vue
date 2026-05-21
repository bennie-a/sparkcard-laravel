<script setup>
    import { ref } from 'vue';

    defineProps({
        url: { type: String, required: true },
        id: { type: Number, required: true },
        height : {type:Number, default:140}
    });
    const dialog = ref(false);

</script>
<template>
    <v-img :src="url"  cover  class="image-position" :height="height" @click="dialog = true"></v-img>
    <v-dialog v-model="dialog" class="text-center" width="400">
        <v-card  class="text-center">
            <v-card-text>
                <v-img :src="url"></v-img>
            </v-card-text>
            <v-card-actions class="d-flex justify-center">
                <v-btn text="閉じる" @click="dialog = false"></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<style scoped>
.image-position:deep(img) {
  object-position: center -40px; /* 右側を基準に表示 */
  cursor: pointer;
}
</style>
