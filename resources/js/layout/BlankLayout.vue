<template>
    <div class="blank-layout">
        <header>
            <div class="ui pointing">
                <div class="header navbar-brand">
                    <router-link to="/" class="navbar-brand"
                        ><i class="bi bi-gem"></i> SPARKCARD</router-link
                    >
                </div>
            </div>
        </header>
        <main id="main" ref="main" class="mt-2 twelve wide column">
            <router-view />
        </main>
    </div>
</template>
<style scoped>
.blank-layout {
  min-height: 100vh;
  background-color: whitesmoke;
}
</style>
