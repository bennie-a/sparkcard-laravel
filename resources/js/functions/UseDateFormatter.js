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
    }
    return {
        toString
    }
}