import { VueFinalModal } from "vue-final-modal";
import { __VLS_internalComponent, __VLS_componentsOption, __VLS_name } from "./ModalButton.vue";

function __VLS_template() {
let __VLS_ctx!: InstanceType<__VLS_PickNotAny<typeof __VLS_internalComponent, new () => {}>> & {};
/* Components */
let __VLS_otherComponents!: NonNullable<typeof __VLS_internalComponent extends { components: infer C; } ? C : {}> & typeof __VLS_componentsOption;
let __VLS_own!: __VLS_SelfComponent<typeof __VLS_name, typeof __VLS_internalComponent & typeof __VLS_publicComponent & (new () => { $slots: typeof __VLS_slots; })>;
let __VLS_localComponents!: typeof __VLS_otherComponents & Omit<typeof __VLS_own, keyof typeof __VLS_otherComponents>;
let __VLS_components!: typeof __VLS_localComponents & __VLS_GlobalComponents & typeof __VLS_ctx;
/* Style Scoped */
type __VLS_StyleScopedClasses = {} &
{ 'modal-container'?: boolean; } &
{ 'modal-content'?: boolean; } &
{ 'modal__content'?: boolean; } &
{ 'modal__action'?: boolean; } &
{ 'modal__close'?: boolean; };
let __VLS_styleScopedClasses!: __VLS_StyleScopedClasses | keyof __VLS_StyleScopedClasses | (keyof __VLS_StyleScopedClasses)[];
/* CSS variable injection */
/* CSS variable injection end */
let __VLS_resolvedLocalAndGlobalComponents!: {} &
__VLS_WithComponent<'VueFinalModal', typeof __VLS_localComponents, "VueFinalModal", "vueFinalModal", "vue-final-modal"> &
__VLS_WithComponent<'loading', typeof __VLS_localComponents, "Loading", "loading", "loading">;
__VLS_components.VueFinalModal; __VLS_components.VueFinalModal; __VLS_components.vueFinalModal; __VLS_components.vueFinalModal; __VLS_components["vue-final-modal"]; __VLS_components["vue-final-modal"];
// @ts-ignore
[VueFinalModal, VueFinalModal,];
({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div; ({} as __VLS_IntrinsicElements).div;
({} as __VLS_IntrinsicElements).i; ({} as __VLS_IntrinsicElements).i;
({} as __VLS_IntrinsicElements).h2; ({} as __VLS_IntrinsicElements).h2;
({} as __VLS_IntrinsicElements).p; ({} as __VLS_IntrinsicElements).p;
({} as __VLS_IntrinsicElements).button; ({} as __VLS_IntrinsicElements).button; ({} as __VLS_IntrinsicElements).button; ({} as __VLS_IntrinsicElements).button; ({} as __VLS_IntrinsicElements).button; ({} as __VLS_IntrinsicElements).button;
__VLS_components.Loading; __VLS_components.loading;
// @ts-ignore
[loading,];
{
let __VLS_0!: 'VueFinalModal' extends keyof typeof __VLS_ctx ? typeof __VLS_ctx.VueFinalModal : 'vueFinalModal' extends keyof typeof __VLS_ctx ? typeof __VLS_ctx.vueFinalModal : 'vue-final-modal' extends keyof typeof __VLS_ctx ? (typeof __VLS_ctx)['vue-final-modal'] : (typeof __VLS_resolvedLocalAndGlobalComponents)['VueFinalModal'];
const __VLS_1 = __VLS_asFunctionalComponent(__VLS_0, new __VLS_0({ ...{}, modelValue: ((__VLS_ctx.showModal)), name: ("confirm"), classes: ("modal-container"), contentClass: ("modal-content"), }));
({} as { VueFinalModal: typeof __VLS_0; }).VueFinalModal;
({} as { VueFinalModal: typeof __VLS_0; }).VueFinalModal;
const __VLS_2 = __VLS_1({ ...{}, modelValue: ((__VLS_ctx.showModal)), name: ("confirm"), classes: ("modal-container"), contentClass: ("modal-content"), }, ...__VLS_functionalComponentArgsRest(__VLS_1));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_0, typeof __VLS_2> & Record<string, unknown>) => void)({ ...{}, modelValue: ((__VLS_ctx.showModal)), name: ("confirm"), classes: ("modal-container"), contentClass: ("modal-content"), });
const __VLS_3 = __VLS_pickFunctionalComponentCtx(__VLS_0, __VLS_2)!;
{
const [{ close }] = __VLS_getSlotParams((__VLS_3.slots!).default);
{
const __VLS_4 = ({} as __VLS_IntrinsicElements)["div"];
const __VLS_5 = __VLS_elementAsFunctionalComponent(__VLS_4);
({} as __VLS_IntrinsicElements).div;
({} as __VLS_IntrinsicElements).div;
const __VLS_6 = __VLS_5({ ...{}, class: ("modal__close"), }, ...__VLS_functionalComponentArgsRest(__VLS_5));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_4, typeof __VLS_6> & Record<string, unknown>) => void)({ ...{}, class: ("modal__close"), });
const __VLS_7 = __VLS_pickFunctionalComponentCtx(__VLS_4, __VLS_6)!;
{
const __VLS_8 = ({} as __VLS_IntrinsicElements)["i"];
const __VLS_9 = __VLS_elementAsFunctionalComponent(__VLS_8);
({} as __VLS_IntrinsicElements).i;
({} as __VLS_IntrinsicElements).i;
const __VLS_10 = __VLS_9({ ...{ onClick: {} as any, }, class: ("bi bi-x"), }, ...__VLS_functionalComponentArgsRest(__VLS_9));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_8, typeof __VLS_10> & Record<string, unknown>) => void)({ ...{ onClick: {} as any, }, class: ("bi bi-x"), });
const __VLS_11 = __VLS_pickFunctionalComponentCtx(__VLS_8, __VLS_10)!;
let __VLS_12 = { 'click': __VLS_pickEvent(__VLS_11.emit!, 'click' as const, ({} as __VLS_FunctionalComponentProps<typeof __VLS_9, typeof __VLS_10>).onClick) };
__VLS_12 = {
click: (close)
};
}
(__VLS_7.slots!).default;
}
{
const __VLS_13 = ({} as __VLS_IntrinsicElements)["h2"];
const __VLS_14 = __VLS_elementAsFunctionalComponent(__VLS_13);
({} as __VLS_IntrinsicElements).h2;
({} as __VLS_IntrinsicElements).h2;
const __VLS_15 = __VLS_14({ ...{}, class: ("ui header"), }, ...__VLS_functionalComponentArgsRest(__VLS_14));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_13, typeof __VLS_15> & Record<string, unknown>) => void)({ ...{}, class: ("ui header"), });
const __VLS_16 = __VLS_pickFunctionalComponentCtx(__VLS_13, __VLS_15)!;
(__VLS_16.slots!).default;
}
{
const __VLS_17 = ({} as __VLS_IntrinsicElements)["div"];
const __VLS_18 = __VLS_elementAsFunctionalComponent(__VLS_17);
({} as __VLS_IntrinsicElements).div;
({} as __VLS_IntrinsicElements).div;
const __VLS_19 = __VLS_18({ ...{}, class: ("modal__content"), }, ...__VLS_functionalComponentArgsRest(__VLS_18));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_17, typeof __VLS_19> & Record<string, unknown>) => void)({ ...{}, class: ("modal__content"), });
const __VLS_20 = __VLS_pickFunctionalComponentCtx(__VLS_17, __VLS_19)!;
{
const __VLS_21 = ({} as __VLS_IntrinsicElements)["p"];
const __VLS_22 = __VLS_elementAsFunctionalComponent(__VLS_21);
({} as __VLS_IntrinsicElements).p;
({} as __VLS_IntrinsicElements).p;
const __VLS_23 = __VLS_22({ ...{}, }, ...__VLS_functionalComponentArgsRest(__VLS_22));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_21, typeof __VLS_23> & Record<string, unknown>) => void)({ ...{}, });
const __VLS_24 = __VLS_pickFunctionalComponentCtx(__VLS_21, __VLS_23)!;
(__VLS_24.slots!).default;
}
(__VLS_20.slots!).default;
}
{
const __VLS_25 = ({} as __VLS_IntrinsicElements)["div"];
const __VLS_26 = __VLS_elementAsFunctionalComponent(__VLS_25);
({} as __VLS_IntrinsicElements).div;
({} as __VLS_IntrinsicElements).div;
const __VLS_27 = __VLS_26({ ...{}, class: ("ui divider"), }, ...__VLS_functionalComponentArgsRest(__VLS_26));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_25, typeof __VLS_27> & Record<string, unknown>) => void)({ ...{}, class: ("ui divider"), });
const __VLS_28 = __VLS_pickFunctionalComponentCtx(__VLS_25, __VLS_27)!;
}
{
const __VLS_29 = ({} as __VLS_IntrinsicElements)["div"];
const __VLS_30 = __VLS_elementAsFunctionalComponent(__VLS_29);
({} as __VLS_IntrinsicElements).div;
({} as __VLS_IntrinsicElements).div;
const __VLS_31 = __VLS_30({ ...{}, class: ("modal__action"), }, ...__VLS_functionalComponentArgsRest(__VLS_30));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_29, typeof __VLS_31> & Record<string, unknown>) => void)({ ...{}, class: ("modal__action"), });
const __VLS_32 = __VLS_pickFunctionalComponentCtx(__VLS_29, __VLS_31)!;
{
const __VLS_33 = ({} as __VLS_IntrinsicElements)["button"];
const __VLS_34 = __VLS_elementAsFunctionalComponent(__VLS_33);
({} as __VLS_IntrinsicElements).button;
({} as __VLS_IntrinsicElements).button;
const __VLS_35 = __VLS_34({ ...{ onClick: {} as any, }, class: ("ui basic teal button"), }, ...__VLS_functionalComponentArgsRest(__VLS_34));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_33, typeof __VLS_35> & Record<string, unknown>) => void)({ ...{ onClick: {} as any, }, class: ("ui basic teal button"), });
const __VLS_36 = __VLS_pickFunctionalComponentCtx(__VLS_33, __VLS_35)!;
let __VLS_37 = { 'click': __VLS_pickEvent(__VLS_36.emit!, 'click' as const, ({} as __VLS_FunctionalComponentProps<typeof __VLS_34, typeof __VLS_35>).onClick) };
__VLS_37 = {
click: (close)
};
(__VLS_36.slots!).default;
}
{
const __VLS_38 = ({} as __VLS_IntrinsicElements)["button"];
const __VLS_39 = __VLS_elementAsFunctionalComponent(__VLS_38);
({} as __VLS_IntrinsicElements).button;
({} as __VLS_IntrinsicElements).button;
const __VLS_40 = __VLS_39({ ...{ onClick: {} as any, }, class: ("ui teal button"), }, ...__VLS_functionalComponentArgsRest(__VLS_39));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_38, typeof __VLS_40> & Record<string, unknown>) => void)({ ...{ onClick: {} as any, }, class: ("ui teal button"), });
const __VLS_41 = __VLS_pickFunctionalComponentCtx(__VLS_38, __VLS_40)!;
let __VLS_42 = { 'click': __VLS_pickEvent(__VLS_41.emit!, 'click' as const, ({} as __VLS_FunctionalComponentProps<typeof __VLS_39, typeof __VLS_40>).onClick) };
__VLS_42 = {
click: (__VLS_ctx.execute)
};
(__VLS_41.slots!).default;
}
(__VLS_32.slots!).default;
}
__VLS_3.slots!['' /* empty slot name completion */];
}
}
{
const __VLS_43 = ({} as __VLS_IntrinsicElements)["button"];
const __VLS_44 = __VLS_elementAsFunctionalComponent(__VLS_43);
({} as __VLS_IntrinsicElements).button;
({} as __VLS_IntrinsicElements).button;
const __VLS_45 = __VLS_44({ ...{ onClick: {} as any, }, class: ("ui fluid teal button"), }, ...__VLS_functionalComponentArgsRest(__VLS_44));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_43, typeof __VLS_45> & Record<string, unknown>) => void)({ ...{ onClick: {} as any, }, class: ("ui fluid teal button"), });
const __VLS_46 = __VLS_pickFunctionalComponentCtx(__VLS_43, __VLS_45)!;
let __VLS_47 = { 'click': __VLS_pickEvent(__VLS_46.emit!, 'click' as const, ({} as __VLS_FunctionalComponentProps<typeof __VLS_44, typeof __VLS_45>).onClick) };
__VLS_47 = {
click: (__VLS_ctx.show)
};
{
let __VLS_48!: 'Slot' extends keyof typeof __VLS_ctx ? typeof __VLS_ctx.Slot : 'slot' extends keyof typeof __VLS_ctx ? typeof __VLS_ctx.slot : (typeof __VLS_resolvedLocalAndGlobalComponents)['slot'];
const __VLS_49 = __VLS_asFunctionalComponent(__VLS_48, new __VLS_48({ ...{}, }));
const __VLS_50 = __VLS_49({ ...{}, }, ...__VLS_functionalComponentArgsRest(__VLS_49));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_48, typeof __VLS_50> & Record<string, unknown>) => void)({ ...{}, });
var __VLS_51 = {};
}
(__VLS_46.slots!).default;
}
{
let __VLS_52!: 'Loading' extends keyof typeof __VLS_ctx ? typeof __VLS_ctx.Loading : 'loading' extends keyof typeof __VLS_ctx ? typeof __VLS_ctx.loading : (typeof __VLS_resolvedLocalAndGlobalComponents)['loading'];
const __VLS_53 = __VLS_asFunctionalComponent(__VLS_52, new __VLS_52({ ...{}, active: ((__VLS_ctx.isLoading)), canCancel: ((false)), isFullPage: ((true)), }));
({} as { loading: typeof __VLS_52; }).loading;
const __VLS_54 = __VLS_53({ ...{}, active: ((__VLS_ctx.isLoading)), canCancel: ((false)), isFullPage: ((true)), }, ...__VLS_functionalComponentArgsRest(__VLS_53));
({} as (props: __VLS_FunctionalComponentProps<typeof __VLS_52, typeof __VLS_54> & Record<string, unknown>) => void)({ ...{}, active: ((__VLS_ctx.isLoading)), canCancel: ((false)), isFullPage: ((true)), });
const __VLS_55 = __VLS_pickFunctionalComponentCtx(__VLS_52, __VLS_54)!;
}
if (typeof __VLS_styleScopedClasses === 'object' && !Array.isArray(__VLS_styleScopedClasses)) {
__VLS_styleScopedClasses["modal__close"];
__VLS_styleScopedClasses["bi"];
__VLS_styleScopedClasses["bi-x"];
__VLS_styleScopedClasses["ui"];
__VLS_styleScopedClasses["header"];
__VLS_styleScopedClasses["modal__content"];
__VLS_styleScopedClasses["ui"];
__VLS_styleScopedClasses["divider"];
__VLS_styleScopedClasses["modal__action"];
__VLS_styleScopedClasses["ui"];
__VLS_styleScopedClasses["basic"];
__VLS_styleScopedClasses["teal"];
__VLS_styleScopedClasses["button"];
__VLS_styleScopedClasses["ui"];
__VLS_styleScopedClasses["teal"];
__VLS_styleScopedClasses["button"];
__VLS_styleScopedClasses["ui"];
__VLS_styleScopedClasses["fluid"];
__VLS_styleScopedClasses["teal"];
__VLS_styleScopedClasses["button"];
}
var __VLS_slots!: {
default?(_: typeof __VLS_51): any;
};
// @ts-ignore
[showModal, showModal, showModal, execute, show, isLoading, isLoading, isLoading,];
return __VLS_slots;
}
