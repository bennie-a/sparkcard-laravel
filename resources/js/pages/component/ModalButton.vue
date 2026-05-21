<script setup>
    import { ref } from 'vue';
    const dialog = ref(false);

    defineProps(
        {msg: { type: String, default: "登録してもよろしいですか?" }}
    );

   const execute = () =>{
        dialog.value = false;
   };
// export default {
//     components: {
//         "vue-final-modal": VueFinalModal,
//     },
//     props: { msg: { type: String, default: "登録してもよろしいですか?" } },
//     emits: ["action"],
//     data() {
//         return {
//             showModal: false,
//         };
//     },
//     methods: {
//         show: function () {
//             this.showModal = true;
//         },
//         execute: function () {
//             this.$store.dispatch("message/clear");
//             this.$emit("action");
//             this.showModal = false;
//         },
//         close:function() {
//             this.showModal = false;
//         }
//     },
// };
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

    <!-- <vue-final-modal
        v-model="showModal"
        name="confirm"
        classes="modal-container"
        content-class="modal-content"
    >
        <div class="modal__close">
            <i class="bi bi-x" @click="close"></i>
        </div>
        <h2 class="ui header">Notice</h2>
        <div class="modal__content">
            <p>{{msg}}</p>
        </div>
        <div class="ui divider"></div>
        <div class="modal__action">
            <v-btn variant="flat" @click="close">
                キャンセル
            </v-btn>
            <v-btn color="teal-lighten-1" @click="execute">OK</v-btn>
        </div>
    </vue-final-modal>-->
</template>
<style scoped>

</style>
