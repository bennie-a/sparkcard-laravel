<template>
    <div class="ui list">
        <div class="item">
            <div class="ui small header">新エキスパンション登録</div>
            <ul>
                <li>
                    <router-link
                        class="nav-link"
                        :class="{ active: $route.path === '/' }"
                        aria-current="page"
                        to="/"
                        >カード登録</router-link
                    >
                </li>
            </ul>
        </div>
        <div class="item">
            <div class="ui small header">商品登録</div>
            <ul>
                <li>
                    <router-link
                        to="/about"
                        class="nav-link"
                        :class="{ active: $route.path === '/about' }"
                        >リンク</router-link
                    >
                </li>
            </ul>
        </div>
        <div class="item">
            <div class="ui small header">商品登録</div>
            <ul>
                <li>
                    <router-link
                        to="/notion/update/status"
                        class="nav-link"
                        :class="{
                            active: $route.path === '/notion/update/status',
                        }"
                        >ステータス一括変更</router-link
                    >
                </li>
            </ul>
        </div>
        <div class="ui divider"></div>
        <span>ver.1.0.0</span>
    </div>
</template>
<style scoped>
.ui.list > .item .header {
    color: white;
    font-weight: 300;
    margin-left: 0.5em;
}
div.item > ul {
    padding-left: 0;
    margin-top: 0.5em;
}
div.item > ul > li {
    list-style: none;
}

span {
    text-align: center;
    display: block;
}

div.item > ul > li > a {
    padding: 1.5em 1em;
    display: block;
    color: white;
}
div.item > ul > li > a:hover {
    background: #2766cc;
}

div.item > ul > li > a.active {
    font-weight: 500;
    background: #2766cc;
    border-left: 5px solid #a333c8;
}
</style>
