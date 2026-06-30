<script setup>
defineProps({vendor:{type:Object, required:true}});

const colorkind  = {
                                    1:"purple",
                                    2:"green",
                                    3:"orange",
                                    4:"grey",
                                    5:"brown"
                                };
</script>
<template>
    <v-chip label :color="colorkind[vendor.id] + '-lighten-1'" variant="flat" density="compact">{{vendor.name}}</v-chip>
</template>
