<template>
    <div class="ui middle center aligned grid">
        <div class="five wide column">
            <h1 class="ui header">
                <i class="linkify icon"></i>
                <div class="content">BASE APIと連携する
                    <div class="sub header">BASE APIと連携するために認可コードを設定します。</div>
                </div>
            </h1>
            <section>
                <h3 class="mt-2 ui top attached header aligned left">
                    認可コードの取得方法
                    <div class="sub header">連携前に認可サーバーから認可コードを取得してください。(別画面に表示されます。)</div>
                    </h3>
                <div class="ui attached segment center aligned">
                    <button  class="ui red button"><i class="external alternate icon"></i>認可サーバーを表示する</button>
                </div>
            </section>
            <section class="mt-2">
                <div class="ui segment">
                    <div class="ui seven column form">
                        <div class="field required">
                            <label>認可コード</label>
                            <input type="text" name="auth-code">
                        </div>

                        <div class="ui center aligned">
                        <button class="ui teal button">
                            <i class="linkify icon"></i>
                            連携する
                        </button>
                        </div>
                    </div>
                </div>
              </section>
            <section class="ui center aligned mt-3">
                <router-link to="/"><i class="arrow left icon"></i>トップページに戻る</router-link>
          </section>
        </div>
    </div>
</template>
<style scoped>
.ui.header .content {
    align-items: left;
}

.ui.form .field {
    text-align: left;
}
</style>
