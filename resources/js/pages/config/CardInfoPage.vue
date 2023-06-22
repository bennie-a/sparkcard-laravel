<template>
    <section class="ui grids">
        <message-area></message-area>
        <div>
            {{ setname }}({{ attr }})
            <ModalButton @action=""
                ><i class="checkmark icon"></i>登録する</ModalButton
            >
        </div>
    </section>
</template>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/MessageArea.vue";
import Datepicker from "@vuepic/vue-datepicker";
import ModalButton from "../component/ModalButton.vue";

export default {
    data() {
        return {
            setname: this.$route.params.setname,
            attr: this.$route.params.attr,
        };
    },
    methods: {
        back: function () {
            this.$router.push("/settings/expansion");
        },
    },
    components: {
        "message-area": MessageArea,
        ModalButton: ModalButton,
    },
};
</script>
<style></style>
