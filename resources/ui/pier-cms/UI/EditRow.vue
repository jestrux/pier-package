<template>
  <div>
    <div class="modal open" v-if="fetchingRecords || !records || !selectedModel || !selectedModel.fields || !selectedRecord">
      <div class="modal-content" style="width: 450px">
        <div class="modal-body">
          <Loader :size="90" />
        </div>
      </div>
    </div>

    <AddRow v-else :values="selectedRecord" />
  </div>
</template>

<script>
import AddRow from "../AddRow";
import { mapState, mapGetters } from "vuex";

export default {
  name: "PierCMSEditRow",
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
    AddRow
  }
};
</script>