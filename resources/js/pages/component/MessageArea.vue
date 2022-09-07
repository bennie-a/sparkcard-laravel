<template>
    <div class="ui info message" v-if="$store.state.msg.success != ''">
        <div class="header">
            {{ $store.state.msg.success }}
        </div>
    </div>
</template>
<script>
export default {};
</script>
