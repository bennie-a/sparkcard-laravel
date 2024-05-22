<template>
     <label class="mercari ui label" v-if="orderId.startsWith('order_')">メルカリShops</label>
</template>
<script>
export default{
    props: {
        orderId: { type: String, required:true, default:"注文" },
    },
    computed:{
        
    }
};
</script>
<style>
label.mercari {
    color: #DB2828!important;
    background: rgb(255, 140, 65,0.2)!important;
}
</style>