// import { mount } from "vue/test-utils";
import { toSurfaceName, toItemName } from "../../composables/CardCollector.js";
describe("画像名", () => {
    test("nonfoil", () => {
        let card = { name: "ヤヴィマヤの沿岸", number:110, foil: {is_foil:false}, exp:{attr:'M15'} };
        let photo = toSurfaceName(card);
        expect('110_M15_thumb.jpg').toBe(photo);
    });
    test("foil", () => {
        let card = { name: "ヤヴィマヤの沿岸", number:110, foil: {is_foil:true}, exp:{attr:'M15'} };
        let photo = toSurfaceName(card);
        expect('110_M15-foil_thumb.jpg').toBe(photo);
    });
});
