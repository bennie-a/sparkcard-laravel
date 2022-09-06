<template>
    <div class="ui segment">
        <h2 class="ui small header">
            <i class="question circle icon"></i>絞り込み条件
        </h2>
        <div class="flex">
            <select class="ui dropdown" v-model="status">
                <option value="ロジクラ要登録">ロジクラ要登録</option>
                <option value="販売保留">販売保留</option>
            </select>
            <div class="ui right labeled input">
                <input
                    type="text"
                    placeholder="価格"
                    v-model="price"
                    @input="
                        (event) =>
                            (value = event.target.value.replace(/[^0-9]/g, ''))
                    "
                />
                <div class="ui basic label">円</div>
            </div>
            <select class="ui dropdown" v-model="isMore">
                <option value="true">以上</option>
                <option value="false">未満</option>
            </select>
            <button class="ui purple button ml-1" @click="search">
                検索する
            </button>
        </div>
    </div>
    <NowLoading></NowLoading>
</template>
<style scoped>
div.flex {
    display: flex;
    align-items: center;
    column-gap: 1rem;
}
</style>
<script>
import NowLoading from "../component/NowLoading.vue";
export default {
    data() {
        return {
            // cards: [],
            status: "ロジクラ要登録",
            price: "",
            isMore: true,
        };
    },
    methods: {
        search: function () {
            console.log(this.price);
            console.log(this.status);
            console.log(this.isMore);
            this.$store.dispatch("setLoad", !this.$store.state.isLoad);
        },
    },
    watch: {
        price: function () {
            const pattern = /[a-zA-Z]/g; // 半角英字のみ不可
            this.price = this.price.replace(pattern, "");
        },
    },
    components: { NowLoading },
};
</script>
