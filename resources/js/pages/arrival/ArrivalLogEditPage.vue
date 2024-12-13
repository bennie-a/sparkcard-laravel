<script setup></script>
<template>
    <div class="ui grid">
        <div class="mt-1 ui seven wide column form">
            <div class="field">
                <label>カード名</label>
                <span>アスモラノマルディカダイスティナカルダカール</span>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>入荷日</label>
                </div>
                <div class="field">
                    <label>言語</label>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>入荷カテゴリ</label>
                </div>
                <div class="field">
                    <label>取引先</label>
                </div>
            </div>
            <div class="two fields">
                <div class="four wide field">
                    <label>原価</label>
                    <div class="ui left icon input">
                        <input type="number" step="1" min="1">
                        <i class="yen sign icon"></i>                       
                    </div>
                </div>
                <div class="four wide field">
                    <label>枚数</label>
                    <div class="ui right labeled input">
                        <input type="number" min="1" step="1">
                        <label class="ui basic label">枚</label>
                    </div>
                </div>
            </div>
            <div class="field">
                <button class="ui basic teal button"><i class="angle double left icon"></i>入荷詳細に戻る</button>
                <button class="ui teal button ml-1"><i class="pencil alternate icon"></i>変更する</button>                    
            </div>
        </div>
        <div class="six wide column">
            <img class="ui medium image" src="https://cards.scryfall.io/png/front/d/8/d80ff101-cfcd-4ffd-a8d4-f47d7d5048d2.png?1646240439" alt="">
        </div>
    </div>
</template>