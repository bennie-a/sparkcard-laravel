import {baseConnected } from "@/stores/auth/baseConnected";
import {LoadStore} from "@/stores/loading/LoadStore.js";
import axios from 'axios';
import { storeToRefs } from 'pinia';
import {MsgStore} from "@/pages/component/msg/MsgStore";

// 認証に関するスクリプト
export const authGuard = (router) => {

    router.beforeEach(async(to, from, next) => {
            const loading  = LoadStore();
            const msgStore = MsgStore();
            router['referrer'] = from;

            msgStore.clear();
            loading.on();
            // BASE API認証
            const requiresBase = to.meta?.requiresBase ?? false;
            const baseStore = baseConnected();
            if (!requiresBase || baseStore.hasConnected()) {
                return next();
            }

            await axios.get('/api/base/oauth/status')
                .then((response) => {
                    const isConnected = response.data.connected;
                    if (isConnected) {
                        baseStore.connect();
                        return next();
                    } else {
                        return next({
                                        path: "/base/auth/",
                                        query: {
                                            redirect: to.fullPath
                                        }
                                    });
                    }
                })
                .catch(() => {
                    return next({
                                    path: "/base/auth/",
                                    query: {
                                        redirect: to.fullPath
                                    }
                                });
                })
            });

    router.afterEach(() => {
        const loading  = LoadStore();
        loading.off();
    });

};
