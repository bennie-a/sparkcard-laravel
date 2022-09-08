<template>
    <div v-if="!$store.getters.isLoad">
        <div class="ui info message" v-if="$store.state.msg.success != ''">
            <div class="header">
                {{ $store.state.msg.success }}
            </div>
        </div>
        <div class="ui negative message" v-if="$store.state.msg.error != ''">
            <div class="header">
                {{ error }}
            </div>
            <p></p>
        </div>
    </div>
</template>
<script>
export default {};
</script>
