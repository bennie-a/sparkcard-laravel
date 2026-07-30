<template>
    <v-chip label density="compact" variant="flat" :color="colorClass(this.name)">
        <strong>{{ this.name }}</strong>
    </v-chip>
</template>
<script>
export default {
    name: "condition",
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
                return colors[name] + "-darken-1";
            };
        },
    },
};
</script>
