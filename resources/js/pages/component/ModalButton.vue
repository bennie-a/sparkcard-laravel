<template>
    <vue-final-modal
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
            <button class="ui basic teal button" @click="close">
                キャンセル
            </button>
            <button class="ui teal button" @click="execute">OK</button>
        </div>
    </vue-final-modal>
    <button class="ui fluid teal icon button" @click="show">
        <slot></slot>
    </button>
</template>
<script>
import { VueFinalModal } from "vue-final-modal";

export default {
    components: {
        "vue-final-modal": VueFinalModal,
    },
    props: { msg: { type: String, default: "登録してもよろしいですか?" } },
    emits: ["action"],
    data() {
        return {
            showModal: false,
        };
    },
    methods: {
        show: function () {
            this.showModal = true;
        },
        execute: function () {
            this.$store.dispatch("message/clear");
            this.$emit("action");
            this.showModal = false;
        },
        close:function() {
            this.showModal = false;
        }
    },
};
</script>
<style scoped>
::v-deep .modal-container {
    display: flex;
    justify-content: center;
    align-items: center;
}
::v-deep .modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    margin: 0 1rem;
    padding: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    background: #fff;
}

.modal__content {
    flex-grow: 1;
    padding-top: 0rem;
}

.modal__action {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-shrink: 0;
    column-gap: 1rem;
}
.modal__close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    font-size: 2rem;
    cursor: pointer;
}
</style>
