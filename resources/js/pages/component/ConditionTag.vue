<template>
    <div class="ui horizonal label" :class="colorClass(this.name)">
        {{ this.name }}
    </div>
</template>
<script>
export default {
    props: { name: { type: String } },
    computed: {
        colorClass: function () {
            return (name) => {
                const colors = {
                    NM: "blue",
                    "NM-": "purple",
                    "EX+": "green",
                    EX: "brown",
                    PLD: "red",
                };
                return colors[name];
            };
        },
    },
};
</script>
<style scoped></style>
