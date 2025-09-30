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
    <article id="upload_form" class="ui mt-1 form grid segment">
        <FileUpload type="csv" @action="uploadFile"/>
    </article>
</template>    

<style>
#upload_form {
    padding: 1rem;
}
</style>