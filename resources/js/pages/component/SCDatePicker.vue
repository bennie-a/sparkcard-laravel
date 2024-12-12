<template>
        <datepicker
        v-model="newSelectedDate"
        input-class-name="dp_custom_input"
        locale="jp"
        :enable-time-picker="false"
        :format="dateFormat"
        @update:model-value="handleUpdate"
        auto-apply
    />
</template>
<script>
import Datepicker from "@vuepic/vue-datepicker";
import { ref} from "vue";
export default {
    components:{
        datepicker:Datepicker
    },
    props: {
        selectedDate:{type:Date, required:true},
    },
    emits:[
        'update'
    ],
    data(){
        return {
            newSelectedDate:ref(this.$props.selectedDate),
        };
    },
    methods:{
        dateFormat: function (date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}/${month}/${day}`;
        },
        handleUpdate:function() {
            this.$emit('update', this.newSelectedDate);
        }
    },
}
 
</script>
<style>
.dp__input {
    padding-left: 2rem!important;
}
</style>