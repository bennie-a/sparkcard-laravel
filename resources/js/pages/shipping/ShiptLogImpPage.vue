<script setup>

    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";
    import pagination from "../component/ListPagination.vue";
    import axios from 'axios';

    const router = useRouter();

    const upload = function() {
        
    };
    const uploadFile = async(file) => {
        const formData = new FormData();
        formData.append('file', file);

        await axios.post('/api/shipping/parse', formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response) => {
                const data = response.data;
                console.log(data);
            }).catch((error) => {
                console.error('Error:', error);
            });
    };

</script>

<template>
    <section id="upload_form" class="ui form grid segment">
        <div class="seven wide column">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </section>
    <section class="mt-3 ui grid">
        <article class="row">
            <div class="four wide left floated column">
                <button class="ui teal button" @click="router.back()">インポート</button>
            </div>
            <div class="seven wide right floated column">
                <label>1-5件 / 10件</label>
                <pagination :page="1" :pageCount="2" @pageChange="(p) => alert(p)"/>
            </div>
        </article>
    </section>
</template>    

<style>
#upload_form {
    padding: 1rem;
}
</style>