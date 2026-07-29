<template>
    <v-app>
        <v-app-bar>
            <v-app-bar-title>
                <router-link to="/">
                     <span class="mdi mdi-diamond-stone"></span> SPARKCARD
                </router-link>
            </v-app-bar-title>
        </v-app-bar>
        <v-main>
            <v-container class="mx-auto">
                <router-view />
                <section class="text-center mt-10">
                    <v-btn variant="plain" prepend-icon="mdi-arrow-left" to="/" color="teal-lighten-1">トップページに戻る</v-btn>
                </section>
            </v-container>
        </v-main>
    </v-app>
</template>
