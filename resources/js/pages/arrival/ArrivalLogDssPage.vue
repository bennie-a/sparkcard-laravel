<script setup>
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const arrival_date = route.params.arrival_date;
const vendor_id = route.params.vendor_id;

</script>
<template>
    {{ arrival_date }}{{ vendor_id }}
</template>