<script setup>
    import { ref } from 'vue';
    const dialog = ref(false);

    defineProps(
        {msg: { type: String, default: "登録してもよろしいですか?" }}
    );

    const emit = defineEmits(['action']);

    const execute = () =>{
        emit('action');
        dialog.value = false;
   };
</script>
<template>
    <v-btn color="teal-lighten-1" @click="dialog=true">
        <slot></slot>
    </v-btn>
    <v-dialog width="auto" v-model="dialog">
        <v-card max-width="400" :title="msg" prepend-icon="mdi-information">
        <v-card-actions>
            <v-btn variant="flat" @click="dialog=false" text="キャンセル"></v-btn>
            <v-btn color="teal-lighten-1" variant="flat" @click="execute" text="OK"></v-btn>
        </v-card-actions>
        </v-card>

    </v-dialog>
</template>
