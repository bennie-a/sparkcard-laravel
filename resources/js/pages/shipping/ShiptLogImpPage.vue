<script setup>

    import { reactive, ref } from 'vue';
    import { useRouter } from 'vue-router';
    import FileUpload from "../component/FileUpload.vue";
    import pagination from "../component/ListPagination.vue";
    import axios from 'axios';

    const router = useRouter();
    const result = reactive([]);
    const resultCount = ref(0);
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
                result.value = response.data;
                resultCount.value = result.value.length;
                console.log(result.value);
            }).catch((error) => {
                console.error('Error:', error);
            });
    };

</script>

<template>
    <div id="upload_form" class="ui form grid segment">
        <div class="seven wide column">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </div>
    <div class="mt-3 ui grid">
        <div class="row">
            <div class="four wide left floated column">
                <button class="ui teal button" @click="router.back()">インポート</button>
            </div>
            <div class="seven wide right floated column">
                <label>1-5件&#47;{{ resultCount }}件</label>
            </div>
        </div>
    </div>
    <div class="mt-2">
        {{ result.value }}
        <div class="ui segment" v-for="(r, index) in result.value" :key="index">
            <div>
               【{{index}}】{{r.order_id}}<label class="ml-1 ui red  label">{{r.shipping_date}}発送</label>
            </div>
            <div class="ui secondary segment">
             <p>〒{{ r.zipcode }}</p>
             <p>{{ r.address }}</p>
             <p class="buyer_name">{{ r.buyer_name }}様</p>
            </div>
            </div>
        <pagination :page="1" :pageCount="2" @pageChange="(p) => alert(p)"/>
    </div>
</template>    

<style>
#upload_form {
    padding: 1rem;
}

.segment > p {
    line-height: 0.2rem;
    color: #444;
}

.segment > .buyer_name {
    font-weight: 700;
    font-size: 1.1rem;
}

</style>