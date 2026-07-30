import { readonly, ref } from "vue";
import axios from "axios";

const cache = new Map();

export function usePromoList() {
    const list = ref([]);
    const loading = ref(false);

    const load = async (setcode) => {
        if (!setcode) {
            list.value = [];
            return;
        }

        // キャッシュがあれば利用
        if (cache.has(setcode)) {
            list.value = cache.get(setcode);
            return;
        }

        loading.value = true;

        try {
            const { data } = await axios.get("/api/promo", {
                params: {
                    setcode,
                },
            });

            cache.set(setcode, data);
            list.value = data;
        } finally {
            loading.value = false;
        }
    };

    return {
        list: readonly(list),
        loading: readonly(loading),
        load,
    };
}
