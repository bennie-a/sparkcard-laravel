<template>
    <div class="ui segment">
        <h2 class="ui small header">
            <i class="question circle icon"></i>絞り込み条件
        </h2>
        <div class="flex">
            <select class="ui dropdown">
                <option value="">ステータス選択</option>
                <option value="ロジクラ要登録">ロジクラ要登録</option>
                <option value="販売保留">販売保留</option>
            </select>
            <div class="ui right labeled input">
                <input type="text" placeholder="価格" />
                <div class="ui basic label">円</div>
            </div>
            <select class="ui dropdown">
                <option value="more">以上</option>
                <option value="less">未満</option>
            </select>
            <button class="ui purple button ml-1" @click="">検索する</button>
        </div>
    </div>
</template>
<style scoped>
div.flex {
    display: flex;
    align-items: center;
    column-gap: 1rem;
}
</style>
