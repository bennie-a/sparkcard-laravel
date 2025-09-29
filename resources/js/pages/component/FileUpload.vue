<template>
  <article  id="upload" class="mt-1 ui grid segment">
    <div class="five wide column">
      <input
      id="embedpollfileinput"
      type="file"
      class="inputfile"
      @change="onFileChange"
      />
      <label for="embedpollfileinput" class="ui teal basic button uploadbutton">
        <i class="file icon"></i>
        選択
      </label>
      <label class="ml-1">{{filename}}</label>
    </div>
    <div class="three wide column">
      <button class="ui teal button" @click="upload"><i class="upload icon"></i>アップロード</button>
    </div>
    <div id="invalid" class="ui mini modal">
        <div class="header">{{ props.type }}ファイルではありません。</div>
        <button id="ok" class="ui teal fluid button" @click="ok">OK</button>
    </div>
  </article>
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

#upload {
        padding: 1rem;
    }

#invalid {
    padding: 5px;
}
</style>

<script setup>
import { useStore } from "vuex"
import { defineProps, defineEmits, ref } from "vue"

const store = useStore()

const props = defineProps({
  type: { type: String, default: "csv" },
})

const emit = defineEmits(["action"]);
const file = ref(null);
const filename = ref("ファイルを選択してください");

// ファイルアップロードイベント
const onFileChange = (e) => {
    store.dispatch("message/clear")

    file.value = e.target.files[0];
    if (!file) return;
    filename.value = file.value.name;
    const fileType = file.value.name.split(".").pop()
    if (fileType !== props.type) {
        $("#invalid").modal("show");
        filename.value = "ファイルを選択してください";
        file.value = null;
        e.target.value = null;
        return
    }
}

const ok = () => {
    $("#invalid").modal("hide");
}

const upload = () => {
    store.dispatch("message/clear");
    emit("action", file)
    store.dispatch("setLoad", false)
}
</script>
