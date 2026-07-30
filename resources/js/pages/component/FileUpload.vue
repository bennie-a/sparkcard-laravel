<template>
    <v-file-input
        :label="'ファイルを選択してください'"
        :accept="'.' + props.type"
        variant="outlined"
        density="compact"
        :prepend-icon="props.icon"
        @change="onFileChange"
        clearable
    />
</template>
<script setup>
import { defineProps, defineEmits, ref } from "vue"
import { MsgStore } from '@/pages/component/msg/MsgStore';

const filekey = ref(0);

const props = defineProps({
  type: { type: String, default: "csv" },
  icon: { type: String, default: "mdi-file-delimited-outline" },
})

const emit = defineEmits(["action"])
const msgStore = MsgStore();

// ファイルアップロードイベント
const onFileChange = (e) => {
  msgStore.clear();
  filekey.value = Date.now();
  const file = e.target.files[0]
  if (!file) return

  emit("action", file)
}
</script>
