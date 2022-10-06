import MercariContents from "../../csv/MercariContents";
describe("カードの状態", () => {
    test("NM", () => {
        let contents = MercariContents();
        let result = contents.getCondition("NM");
        expect(result).toBe("1");
    });
    test("NM-", () => {
        let contents = MercariContents();
        let result = contents.getCondition("NM-");
        expect(result).toBe("2");
    });
    test("EX+", () => {
        let contents = MercariContents();
        let result = contents.getCondition("EX+");
        expect(result).toBe("3");
    });
    test("EX", () => {
        let contents = MercariContents();
        let result = contents.getCondition("EX");
        expect(result).toBe("4");
    });
    test("PLD", () => {
        let contents = MercariContents();
        let result = contents.getCondition("PLD");
        expect(result).toBe("5");
    });
    test("その他", () => {
        let contents = MercariContents();
        let result = contents.getCondition("DAMAGE");
        expect(result).toBe("3");
    });
});
