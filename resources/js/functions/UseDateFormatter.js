// Date型のオブジェクトを変換するクラス
export default function () {
    const toString = (date) => {
        if (typeof date === "string") {
            return date;
        }
        if (date != null) {
            return date.toLocaleDateString("ja-JP", {year:"numeric", month:"2-digit",day:"2-digit" });
        }
        return null;
    };
    const toDate = (value) => {
        if (value == null || value === '') {
            return value;
        }
        const [y, m, d] = value.split('/');
        return new Date(Number(y), Number(m) - 1, Number(d));
    };
    return {
        toString, toDate
    }
}
