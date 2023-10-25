import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import SearchForm from "../component/SearchForm.vue";
import CSVUpload from "../component/CSVUpload.vue";
import DownloadButton from "../component/DownloadButton.vue";
import { NOTION_STATUS } from "../../cost/NotionStatus";

export default (await import('vue')).defineComponent({
data() {
return {
isPrinting: false,
isPublic: true,
canCategory: true,
contentMap: {},
};
},
mounted: function () {
this.$store.dispatch("search/status", NOTION_STATUS.tobase);
},

methods: {
csvUpload: function (file) {
this.$papa.parse(file, {
header: true,
complete: function (results) {
let csvdata = results.data;
csvdata.map((line) => {
this.contentMap[line["商品名"]] = line["商品ID"];
});
const card = this.$store.getters.card;
card.map((c) => {
let code = this.contentMap[toItemName(c)];
if (code !== undefined) {
c["baseId"] = code;
}
});
this.canCategory = false;
}.bind(this),
});
console.log(file.name);
},
},
components: {
"now-loading": NowLoading,
"card-list": CardList,
"message-area": MessageArea,
"search-form": SearchForm,
"file-upload": CSVUpload,
"download-button": DownloadButton,
},
});
