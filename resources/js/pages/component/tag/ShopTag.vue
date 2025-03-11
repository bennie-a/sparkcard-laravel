<template>
     <label class="mercari ui label" v-if="orderId.startsWith('order_')">メルカリShops</label>
     <label class="base ui label" v-else>BASEショップ</label>
</template>
<script>
export default{
    props: {
        orderId: { type: String, required:true },
    },
};
</script>
<style>
label.mercari {
    color: #DB2828!important;
    background: rgb(255, 140, 65,0.2)!important;
}
label.base {
    color:#1C9C3A!important;
    background: rgb(33, 186, 69,0.2)!important;
}
</style>