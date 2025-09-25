<script setup>

    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";

    const router = useRouter();
    const filename = ref("ファイルを選択してください");
</script>

<template>
    <article id="upload" class="mt-1 ui grid segment">
        <div class="six wide column">
            <FileUpload type="csv" @action="(file) => console.log(file)"/><label>{{ filename }}</label>
        </div>
        <div class="three wide column">
            <button class="ui teal button"><i class="upload icon"></i>アップロード</button>
        </div>
    </article>
</template>    

<style>
    #upload {
        padding: 1rem;
    }
</style>