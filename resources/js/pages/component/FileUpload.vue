<template>
  <input
    id="embedpollfileinput"
    type="file"
    class="inputfile"
    @change="onFileChange"
  />
  <label for="embedpollfileinput" class="ui teal basic button uploadbutton">
    <i class="file icon"></i>
    選択する
  </label>
  <label class="ml-2">{{filename}}</label>
</template>

<style scoped>
.inputfile {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}
</style>

<script setup>
import { useStore } from "vuex"
import { defineProps, defineEmits, ref } from "vue"

const store = useStore()

const props = defineProps({
  type: { type: String, default: "csv" },
})

const emit = defineEmits(["action"])
const filename = ref("ファイルを選択してください");

// ファイルアップロードイベント
const onFileChange = (e) => {
  store.dispatch("message/clear")

  const file = e.target.files[0]
  if (!file) return
  filename.value = file.name;
  const fileType = file.name.split(".").pop()
  if (fileType !== props.type) {
    store.dispatch("message/error", `ファイルは${props.type}ファイルを選択してください。`)
    return
  }

  emit("action", file)
  store.dispatch("setLoad", false)
}
</script>
