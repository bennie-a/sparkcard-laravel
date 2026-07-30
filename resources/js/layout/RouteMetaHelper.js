export const resolveMetaValue = (metaValue, route) => {
    return typeof metaValue === 'function' ? metaValue(route) : metaValue ?? '';

};
