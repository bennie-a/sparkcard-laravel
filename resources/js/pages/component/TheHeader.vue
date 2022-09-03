<template>
    <header>
        <nav class="ui grid menu">
            <div class="three wide column left floated">
                <router-link to="/" class="navbar-brand"
                    ><i class="bi bi-gem"></i> SPARKCARD-Larvel</router-link
                >
            </div>
        </nav>
    </header>
</template>

<style scoped>
.nav-item {
    margin-right: 2em;
}
.navbar-brand {
    font-size: 1.8em;
    font-family: "Exo 2", sans-serif;
    background-image: linear-gradient(to top right, #1e50a2 0%, #d43f8d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    vertical-align: top;
}
.nav-link {
    font-weight: 500;
    padding-bottom: 2px;
    color: #999 !important;
}
a.navbar-brand i {
    font-size: 0.85em;
}
.nav-link.active {
    font-weight: 500;
    border-bottom: solid 3px #4753a2;
    color: #4753a2 !important;
}
</style>
<script>
export default {
    computed: function navPoint() {
        return this.$route.fullPath;
    },
};
</script>
