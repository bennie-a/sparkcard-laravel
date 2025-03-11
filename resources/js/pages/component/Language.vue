<script setup>
import { ref } from "vue";
    const lang = defineModel();
    const cat = ["JP", "EN", "IT", "CS", "CT"];
</script>
<template>
    <div class="inline field radio-button">
        <label v-for="c in cat" :key="c">
            <input v-model="lang" type="radio" :value="c">
            <span>{{ c }}</span>
        </label>
    </div>
</template>
<style>
.radio-button > :first-child,
.radio-button > label span {
    margin-right: 0.5rem !important;

    cursor: pointer;
}

.ui.form .inline.field > :first-child {
    margin-right: 0 !important;
}
.radio-button {
    line-height: 3;
}
.radio-button > label input {
    display: none; /* デフォルトのinputは非表示にする */
}
.radio-button > label span {
    padding: 5px 10px !important; /* 上下左右に余白をトル */
    border-radius: 5px;
    color: var(--teal);
    border: 1px solid var(--teal);
}

label input:checked + span {
    color: #fff; /* 文字色を白に */
    background: var(--teal); /* 背景色を薄い赤に */
    border: 0;
}
</style>