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
            <h1 class="ui header">
                <v-icon icon="mdi-link-variant"></v-icon>
                <div class="content">BASE APIと連携する
                    <div class="sub header">BASE APIと連携するために認可コードを設定します。</div>
                </div>
            </h1>
            <div class="ui negative message" v-if="error">
                <div class="header">
                    {{ error }}
                </div>
            </div>
            <section>
                <h3 class="mt-2 ui top attached header aligned left">
                    認可コードの取得方法
                    <div class="sub header">連携前に認可サーバーから認可コードを取得してください。(別画面に表示されます。)</div>
                    </h3>
                <div class="ui attached segment center aligned">
                    <button  class="ui red button" @click="toAuthServer"><span class="mdi mdi-open-in-new"></span> 認可サーバーを表示する</button>

                </div>
            </section>
            <section class="mt-2">
                <div class="ui segment">
                    <div class="ui seven column form">
                        <div class="field required">
                            <label>認可コード</label>
                            <input type="text" name="auth-code" v-model="code">
                        </div>

                        <div class="ui center aligned">
                        <button class="ui teal button" @click="connect">
                            <span class="mdi mdi-link-variant"></span>
                            連携する
                        </button>
                        </div>
                    </div>
                </div>
              </section>
            <section class="ui center aligned mt-3">
                <router-link to="/"><v-icon icon="mdi-arrow-left"></v-icon> トップページに戻る</router-link>
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
