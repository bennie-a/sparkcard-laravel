<script setup>
import { useRoute, useRouter } from 'vue-router';
import { baseConnected } from '@/stores/auth/baseConnected';
import { mount } from '@vue/test-utils';
import { onMounted, ref } from 'vue';
import Loading from "vue-loading-overlay";
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const isLoading = ref(false);
const authUrl = ref("");
const code = ref("");
const error = ref("");

const toAuthServer = async() => {
    isLoading.value = true;
    await axios.get('/api/base/oauth/connect')
        .then((response) => {
            const authorizationEndpoint = response.data.url;
            window.open(authorizationEndpoint, '_blank');
        })
        .catch((e) => {
            console.error(e.statusCode);
        })
        .finally(() => {
            isLoading.value = false;
        });
};
const connect = async() => {
    isLoading.value = true;
    const query = {
        code: code.value
    };

    await axios.post('/api/base/oauth/callback', query)
        .then((response) => {
            console.log(response.data);
            const baseStore = baseConnected();
            baseStore.connect();
            router.push(route.query.redirect);
        })
        .catch((e) => {
            error.value = e.response.data.detail;
        })
        .finally(() => {
            isLoading.value = false;
        });
};
</script>
<template>
    <div class="ui middle center aligned grid">
        <div class="six wide column">
            <div class="ui negative message" v-if="error">
                <div class="header">
                    {{ error }}
                </div>
            </div>
            <section class="w-75 mx-auto">
                <h1 class="text-headline-medium">
                    BASE APIと連携する
                    <div class="text-title-medium text-grey-darken-1 font-weight-regular">
                        BASE APIと連携するために認可コードを設定します。
                    </div>
                </h1>
                <v-card  rounded>
                    <v-card-item class="bg-grey-lighten-3">
                        <v-card-title>認可コードの取得方法</v-card-title>
                        <v-card-subtitle>連携前に認可サーバーから認可コードを取得してください。(別画面に表示されます。)</v-card-subtitle>
                    </v-card-item>
                    <v-card-actions class="pa-5 w-50 mx-auto">
                        <v-btn color="red-darken-2" @click="toAuthServer" variant="flat" prepend-icon="mdi-open-in-new" block> 認可サーバーを表示する</v-btn>
                    </v-card-actions>
                </v-card>
                <v-card  rounded class="mt-10 pt-5 pb-5">
                    <v-card-text class="mb-0 w-75 mx-auto">
                        <v-text-field v-model="code" label="認可コード"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="w-25 mx-auto">
                        <v-btn color="teal-lighten-1" @click="connect" variant="flat" prepend-icon="mdi-link-variant" block>連携する</v-btn>
                    </v-card-actions>
                </v-card>
            </section>
        </div>
    </div>
        <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />

</template>
<style scoped>
.ui.header .content {
    text-align: left;
}

.negative .message > header {
    text-align: left;
}

.ui.form .field {
    text-align: left;
}
</style>
