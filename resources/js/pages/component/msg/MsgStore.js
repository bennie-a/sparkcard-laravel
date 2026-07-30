import { defineStore } from "pinia";
import { ref } from "vue";

// メッセージを格納するストアを定義する。
export const MsgStore = defineStore('message', {
    state:() => ({
        visible:false,
        type:'success',
        message:'',
        errMsgs:[]
    }),

    actions: {
        success(message) {
            this.type = 'success'
            this.message = message
            this.visible = true
        },

        error(message) {
            this.type = 'error'
            if (!Array.isArray(message)) {
                this.errMsgs = [message]
            } else {
                this.errMsgs = message
            }
            this.visible = true
        },

        warning(message) {
            this.type = 'warning'
            this.errMsgs = [message]
            this.visible = true
        },

        info(message) {
            this.type = 'info'
            this.message = message
            this.visible = true
        },

        clear() {
            this.visible = false
            this.message = ''
            this.errMsgs = []
        }
    }
})
