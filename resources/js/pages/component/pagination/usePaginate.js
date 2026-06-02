// ページネーションのロジッククラス
import { computed, ref } from "vue";

export function usePagenate(items, itemPerPage = 12) {
    const page = ref(1);

    // 総ページ数を取得する。
    const pageCount = computed(() =>{
        if (items.value.length == 0) {
            return 0;
        }
        return Math.ceil(items.value.length / itemPerPage);
    });

    // ページ数をクリックした際の内容を取得する。
    const paginatedList = computed(() => {
        if (items.value.length == 0) {
            return [];
        }
        const start = (page.value - 1) * itemPerPage;
        const end = start + itemPerPage;

        return items.value.slice(start, end);
    });

    const resetPage = () => {
        page.value = 1;
    }

    return {
        page,
        pageCount,
        paginatedList,
        resetPage
    }
}
