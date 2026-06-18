import * as yup from 'yup';

// 半角英数字チェック
yup.addMethod(yup.string, 'customAlpha', function () {
  return this.matches(
    /^[A-Za-z0-9]+$/,
    '半角英数字のみで入力してください。'
  );
});

export default yup;
