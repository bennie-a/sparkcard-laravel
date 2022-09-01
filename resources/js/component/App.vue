<script setup>
import TheHeader from "../pages/component/TheHeader.vue";
</script>
<template>
    <TheHeader></TheHeader>
    <div class="container">
        <div class="row">
            <div class="col-10 offset-md-1">
                <main>
                    <!-- <h1 class="h3 mb-1" v-show="$route.meta.title">
                        {{ $route.meta.title }}
                    </h1>
                    <p>{{ $route.meta.description }}</p>

                    <div
                        class="alert alert-danger"
                        role="alert"
                        v-show="errMessage"
                    >
                        {{ errMessage }}
                    </div> -->
                    <router-view />
                </main>
            </div>
        </div>
    </div>
</template>
