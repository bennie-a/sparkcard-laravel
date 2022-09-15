<template>
    <div id="logo" class="three column row white">
        <router-link to="/" class="navbar-brand"
            ><i class="bi bi-gem"></i> SPARKCARD</router-link
        >
    </div>
    <div id="sidemenu" class="three column row">
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
                <div class="ui small header">ロジクラ</div>
                <ul>
                    <li>
                        <router-link
                            to="/logikura/newitem"
                            class="nav-link"
                            :class="{
                                active: $route.path === '/logikura/newitem',
                            }"
                            ><i class="download icon"></i
                            >商品登録CSVダウンロード</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">BASEショップ</div>
                <ul>
                    <li>
                        <router-link
                            to="/base/newitem"
                            class="nav-link"
                            :class="{ active: $route.path === '/base/newitem' }"
                            ><i class="download icon"></i
                            >BASE用CSVダウンロード</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="item">
                <div class="ui small header">Notion操作</div>
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
    </div>
</template>
<style scoped>
#logo {
    background: white;
    padding: 1rem 0.5rem;
}
.navbar-brand {
    font-size: 1.8em;
    font-family: "Exo 2", sans-serif;
    background-image: linear-gradient(to top right, #1e50a2 0%, #d43f8d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    vertical-align: top;
}

#sidemenu {
    padding-top: 2rem;
}
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
    line-height: 1.6;
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
