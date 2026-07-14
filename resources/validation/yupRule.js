import * as yup from 'yup';

// 半角英数字チェック
yup.addMethod(yup.string, 'customAlpha', function () {
    return this.test('customAlpha', function(value) {
        if (value == null || value === '') {
            return true;
        }
        const isValid = /^[A-Za-z0-9]+$/.test(value);
        return (isValid || this.createError({message:`${this.schema.spec.label}は半角英数字のみで入力してください。`}));
    });
});

export default yup;
