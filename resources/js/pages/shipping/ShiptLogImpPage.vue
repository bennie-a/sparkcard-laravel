<script setup>

    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";

    const router = useRouter();

    const uploadFile = (file) => {
        alert(file.value.name);
    };

</script>

<template>
    <FileUpload type="csv" @action="uploadFile"/>
</template>    

<style>
</style>