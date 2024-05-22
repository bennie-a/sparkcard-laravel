<template>
        <datepicker
        v-model="$props.selectedDate"
        input-class-name="dp_custom_input"
        locale="jp"
        :enable-time-picker="false"
        :format="dateFormat"
    />
    <h3>ユーザー</h3>
    {{ $props.user }}
    <h3>コピーしたユーザー</h3>
    {{ copyUser }}
    <button class="ui button" @click="handleUpdate">点数を50点にする</button>
</template>
<script>
import Datepicker from "@vuepic/vue-datepicker";
import { ref, toRefs, watch } from "vue";
export default {
    components:{
        datepicker:Datepicker
    },
    props: {
        selectedDate:{type:Date, required:true},
        user:{type:Object, required:true}
    },
    emits:[
        'update'
    ],
    setup(props) {
        const {user} = toRefs(props);
        // watch(user, () => copyUser.value = JSON.parse(JSON.stringify(user.value)), {deep:true});
    },
    data(){
        return {
            copyUser:ref(JSON.parse(JSON.stringify(this.$props.user)))
            // selectedDate:new Date
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
            this.copyUser.score.english=50;
            this.$emit('update', 100);
        }
    },
}
 
</script>