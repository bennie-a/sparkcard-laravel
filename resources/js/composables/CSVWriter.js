// CSVファイル形式で整形して1列分の文字列として返す。
export const toCsv = (lines) => {
    let csv = "";
    for (const index in lines) {
        if (lines[index] == "\r") {
            lines[index] = "";
        }
        csv += lines[index];
        if (index != lines.length - 1) {
            csv += ",";
        }
    }
    return csv + "\n";
};

// CSVファイルを出力する。
export const write = (contents, filename) => {
    let blob = new Blob([contents], { type: "text/csv" });
    let link = document.createElement("a");
    link.href = window.URL.createObjectURL(blob);

    link.download = filename;
    link.click();
};

export const writeCsv = (header, cards, filename, pushFn) => {
    let downloadcsv = header;
    cards.forEach((c, index) => {
        let array = [];
        pushFn(array, c, index);
        downloadcsv += array.join(",") + "\n";
    });
    write(downloadcsv, filename);
};
