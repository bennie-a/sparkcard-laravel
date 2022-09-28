// import { mount } from "vue/test-utils";
import { toPhotoName } from "../../composables/CardCollector.js";
describe("画像名", () => {
    test("記号無し", () => {
        let card = { name: "ヤヴィマヤの沿岸", isFoil: false };
        let photo = toPhotoName(card);
        expect(photo).toBe(card.name);
    });
    test("<>を含む", () => {
        let card = { name: "ヤヴィマヤの沿岸<フルアート版>", isFoil: false };
        let photo = toPhotoName(card);
        expect(photo).toBe("ヤヴィマヤの沿岸フルアート版");
    });
    test("、を含む", () => {
        let card = {
            name: "最後の血の長、ドラーナ",
            isFoil: false,
        };
        let photo = toPhotoName(card);
        expect(photo).toBe("最後の血の長ドラーナ");
    });
    test(",を含む", () => {
        let card = {
            name: "Baru,Wurmspeaker",
            isFoil: false,
        };
        let photo = toPhotoName(card);
        expect(photo).toBe("BaruWurmspeaker");
    });
    test("半角スペースを含む", () => {
        let card = {
            name: "Baru Wurmspeaker",
            isFoil: false,
        };
        let photo = toPhotoName(card);
        expect(photo).toBe("BaruWurmspeaker");
    });
    test("Foilがtrue", () => {
        let card = { name: "ヤヴィマヤの沿岸", isFoil: true };
        let photo = toPhotoName(card);
        expect(photo).toBe(card.name + "-Foil");
    });
});
