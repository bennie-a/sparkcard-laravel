<template>
    <message-area></message-area>
    <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="five wide field">
                <label>カード名(一部)</label>
                <input type="text" v-model="cardname" />
            </div>

            <div class="five wide field">
                <label for="">セット名(一部)</label>
                <div class="ui input">
                    <input type="text" v-model="setname" />
                </div>
            </div>
            <div class="field">
                <label style="visibility: hidden">検索ボタン</label>
                <button
                    id="search"
                    class="ui button teal ml-1"
                    @click="search"
                    :class="{ disabled: cardname == '' && setname == '' }"
                >
                    検索する
                </button>
            </div>
        </div>
    </article>
    <article>
        <loading
            :active.sync="isLoading"
            :can-cancel="true"
            :on-cancel="onCancel"
            :is-full-page="true"
        ></loading>
    </article>
</template>

<script>
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import SearchForm from "../component/SearchForm.vue";
import DownloadButton from "../component/DownloadButton.vue";
import Loading from "vue-loading-overlay";

export default {
    data() {
        return {
            cardname: "",
            setname: "",
            isLoading: false,
        };
    },
    methods: {
        search: function () {
            let self = this;
            self.isLoading = true;
            // simulate AJAX
            setTimeout(function () {
                self.isLoading = false;
                console.log("load off");
            }, 1000);
        },
        onCancel: function () {
            console.log("User cancelled the loader.");
        },
    },
    components: {
        loading: Loading,
        "card-list": CardList,
        "message-area": MessageArea,
    },
};
</script>
