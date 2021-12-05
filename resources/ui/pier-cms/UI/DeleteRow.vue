<template>
  <div class="modal open">
    <div class="modal-content" style="width: 450px">
      <div class="modal-body overflow-y-auto" style="max-height: 480px">
        <p class="m-0 pt-2 px-4 text-xl text-center">
          Are you sure you want to permanently delete
          <strong>{{ recordLabel }}</strong
          >?
        </p>
      </div>
      <div class="modal-buttons">
        <button
          class="p-2 font-bold text-sm tracking-wider"
          :class="{ 'pointer-events-none opacity-50': deletingRecord }"
          @click="$router.replace(`/${selectedModelName}`)"
        >
          No, Cancel
        </button>
        <button
          class="bg-red-100 border-red-200 font-semibold px-4 py-2 rounded text-red-600 text-sm tracking-wider"
          :class="{ 'pointer-events-none opacity-50': deletingRecord }"
          @click="deleteRow(null, true)"
        >
          {{ deletingRecord ? "Deleting..." : "Yes, Delete" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapState } from "vuex";
export default {
  name: "PierCMSDeleteRow",
  props: {
    rowId: {
      type: String,
      required: true,
    },
  },
  mounted() {
    if (!this.records && !this.fetchingRecords) {
      this.$store.dispatch("fetchRecords");
      this.$store.subscribe((mutation) => {
        if (mutation.type === "SET_RECORDS")
          this.$store.dispatch("setSelectedRecord", this.rowId);
      });
    } else this.$store.dispatch("setSelectedRecord", this.rowId);
  },
  beforeDestroy() {
    this.$store.dispatch("setSelectedRecord", null);
  },
  computed: {
    ...mapState(["selectedModelName", "deletingRecord"]),
    ...mapGetters(["selectedModel", "selectedRecord"]),
    recordLabel() {
      if (!this.selectedRecord || !this.selectedModel) return "this record";
      return this.selectedRecord[this.selectedModel.display_field];
    },
  },
  methods: {
    deleteRow() {
      this.$store.dispatch("removeRecord", this.rowId);
    },
  },
  watch: {
    rowId: function (rowId) {
      this.$store.dispatch("setSelectedRecord", rowId);
    },
    selectedModel: function (newValue) {
      this.$store.dispatch("fetchRecords");
    },
  },
};
</script>