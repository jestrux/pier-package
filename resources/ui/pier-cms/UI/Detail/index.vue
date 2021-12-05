<style scoped>
.pier-th.image,
.pier-th.phone,
.pier-th.email,
.pier-th.video,
.pier-th.rating,
.pier-th.boolean,
.pier-th.date {
  text-align: center;
}
</style>
<template>
  <div class="h-screen flex-1 flex flex-col relative">
    <header id="mainNav">
      <router-link :to="`${$route.path.replace('detail/' + rowId, 'list')}`" class="inline-flex items-center border-none bg-transparent border-orange-500 text-gray-600 mr-2 py-1 px-2">
        <svg class="mr-2" fill="none" stroke="currentColor" height="20" width="20" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>

        Go Back
      </router-link>

      <span class="flex-1"></span>
    </header>
        
    <div id="mainContent" class="py-12">
      <Loader :size="90" v-if="fetchingRecords || !records || !selectedModel || !selectedModel.fields || !selectedRecord" />
      <div
        v-else
        class="px-8 pt-2 pb-8 border-red border-2 rounded-md mx-auto"
        style="width:100%; max-width: 800px;"
      >
        <TableRow :fields="selectedModel.fields" :data="selectedRecord" />
      </div>
    </div>
  </div>
</template>

<script>
import TableRow from "./TableRow";
import { mapState, mapGetters } from "vuex";

export default {
  name: "PierCMSDetail",
  props: {
    rowId: {
      type: String,
      required: true
    }
  },
  mounted() {
    if (!this.records && !this.fetchingRecords) {
      this.$store.dispatch("fetchRecords");
      this.$store.subscribe(mutation => {
        if (mutation.type === "SET_RECORDS")
          this.$store.dispatch("setSelectedRecord", this.rowId);
      });
    } else this.$store.dispatch("setSelectedRecord", this.rowId);
  },
  beforeDestroy() {
    this.$store.dispatch("setSelectedRecord", null);
  },
  computed: {
    ...mapState(["records", "fetchingRecords", "selectedModelName"]),
    ...mapGetters(["selectedModel", "selectedRecord"])
  },
  watch: {
    rowId: function(rowId) {
      this.$store.dispatch("setSelectedRecord", rowId);
    },
    selectedModel: function(newValue){
      this.$store.dispatch("fetchRecords");
    }
  },
  components: {
    TableRow
  }
};
</script>