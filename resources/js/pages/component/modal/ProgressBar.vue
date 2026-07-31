<script setup>
import { computed} from 'vue';

// 進捗率表示コンポーネント
const visible = defineModel('visible', {type:Boolean, required:true});
const completed = defineModel('completed', {type:Number, required:true});

const props = defineProps({
    completed:{type:Number, required:true},// 完了件数
    total:{type:Number, required:true}// 全体件数
});

const percent = computed(() => {
    if (props.completed == 0 || props.total <= 0) {
        return 0;
    }

    return Math.round(props.completed / props.total * 100);
});
</script>
<template>
    <v-dialog v-model="visible" width="60%">
        <v-sheet class="pa-10 pb-6 text-center">
            <v-progress-linear :model-value="percent" height="15" color="blue-darken-4" rounded></v-progress-linear>
             <div class="mt-5 text-title-large">
                {{percent}}&#37;
            </div>
        </v-sheet>
    </v-dialog>
</template>
