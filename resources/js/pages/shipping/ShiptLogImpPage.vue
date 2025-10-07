<script setup>

    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";

    const router = useRouter();

    const uploadFile = async(file) => {
        alert(file.name);
    };

</script>

<template>
    <article id="upload_form" class="ui mt-1 form grid segment">
        <div class="five wide columns">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </article>
</template>    

<style>
#upload_form {
    padding: 1rem;
}
</style>