import{ store} from '@/store';
import {baseConnected } from "@/stores/auth/baseConnected";
import { storeToRefs } from 'pinia';

// 認証に関するスクリプト
export const authGuard = (router) => {

    router.beforeEach((to, from, next) => {
            router['referrer'] = from;
            store.dispatch("loading/start");
            // BASE API認証
            const requiresBase = to.meta?.requiresBase ?? false;
            if (!requiresBase) {
                return next();
            }

            const baseStore = baseConnected();
            const {isConnected, expiresAt} = storeToRefs(baseStore);

            if (!isConnected.value) {
                return next({
                                path: "/base/auth/",
                                query: {
                                    redirect: to.fullPath
                                }
                            });
            }
            next();
    });

    router.afterEach(() => {
        store.dispatch("loading/stop");
    });

};
