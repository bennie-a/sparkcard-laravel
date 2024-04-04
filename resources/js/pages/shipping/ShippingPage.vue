<template>
    <article class="mt-2">
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="two wide">注文ID</th>
                    <th class="two wide">ショップ名</th>
                    <th class="two wide">購入者名</th>
                    <th class="five wide">住所</th>
                    <th class="two wide">発送日</th>
                    <th><i class="bi bi-chevron-double-down"></i></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>order_5qNafhCKSn7jXNp4rwVC4k</td>
                    <td>メルカリShops</td>
                    <td>三崎健太</td>
                    <td><div>〒252-0206</div>
<span>神奈川県相模原市中央区淵野辺2-31-12-703プレシス桜レジデンス</span></td>
                    <td>2024/02/04</td>
                    <td><i class="bi bi-chevron-down"></i></td>
                </tr>
                <tr>
                    <td colspan="4">【UMA】再誕のパターン[JP][緑]<span class="ui horizonal label green">EX+</span></td>
                    <td>1枚</td>
                </tr>
                <!-- <tr
                    v-for="(s, index) in $store.getters.sliceCard"
                    :key="index"
                >
                    <td>{{ s.id }}</td>
                    <td>
                        <h4 class="ui image header">
                            <img
                                :src="s.image_url"
                                class="ui mini rounded image"
                                @click="$refs.modal[index].showImage(s.id)"
                            >
                            <div class="content">
                                {{ s.cardname
                                }}<foiltag :isFoil="s.isFoil"/>
                                <div class="sub header">{{ s.setname }}</div>
                            </div>
                            <image-modal
                                :url="s.image_url"
                                :id="s.id"
                                ref="modal"
                            />
                        </h4>
                    </td>
                    <td class="center aligned">{{ s.language }}</td>
                    <td class="center aligned">
                        <condition :name="s.condition"/>
                    </td>
                    <td class="center aligned">{{ s.quantity }}</td>
                    <td class="right aligned">{{ s.updated_at }}</td>
                </tr> -->
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination/>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
    </article>
</template>