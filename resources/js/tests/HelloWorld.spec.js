import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import HelloWorld from "../component/HelloWorld.vue";

describe("HelloWorld", () => {

    it("メッセージが表示される", () => {

        const wrapper = mount(HelloWorld);

        expect(wrapper.text()).toContain("Vitest works!");

    });

});
