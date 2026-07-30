<script setup>
    import Loading from "vue-loading-overlay";
    const isLoading = defineModel({ type: Boolean, required: true });
</script>
<template>
    <Loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
