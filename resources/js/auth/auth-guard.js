import{ store} from '@/store';

// 認証に関するスクリプト
export const authGuard = (router) => {

    router.beforeEach((to, from, next) => {
            router['referrer'] = from;
            store.dispatch("loading/start");
            // BASE API認証
            if (to.path === "/base/shipt/import") {
                return next('/base/auth/');
            }
            next();
    });

    router.afterEach(() => {
        store.dispatch("loading/stop");
    });

};
