<template>
  <div class="modal open">
    <div
      class="modal-content rounded-t-md"
      style="
        width: 570px;
        align-self: flex-start;
        margin-top: 3rem;
        border-radius: 12px;
      "
    >
      <div class="modal-title">
        <h3 class="title">New {{ model.name }}</h3>
      </div>

      <form action="#" method="POST" @submit.prevent="saveRow">
        <div
          :key="reloadFields"
          class="modal-body overflow-y-auto"
          style="padding-top: 10px; padding-bottom: 17px; max-height: 480px"
        >
          <pier-form-fields :fields="model.fields" :values="record" />
        </div>

        <div class="modal-buttons rounded-b-md">
          <button
            class="p-2 text-sm"
            :class="{
              'pointer-events-none opacity-50': saving || uploadingFile,
            }"
            type="reset"
            @click="$emit('close')"
          >
            CANCEL
          </button>

          <button
            class="text-sm bg-primary text-white py-2 px-4 rounded"
            :class="{
              'pointer-events-none opacity-50': saving || uploadingFile,
            }"
            type="submit"
          >
            {{
              saving ? `SAVING ${modelName.toUpperCase()}...` : "SAVE CHANGES"
            }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import PierFormFields from "../../../form-fields";

export default {
  name: "AddRowForm",
  props: {
    values: Object,
    model: Object,
    saving: Boolean,
  },
  mounted() {
    // if (this.values) {
    //   this.record = this.values;
    //   this.reloadFields = "be best";
    // }
  },
  data: function () {
    return {
      reloadFields: "be",
    };
  },
  computed: {
    record: function () {
      if (this.values) return this.values;

      if (!this.modelFieldMap) return {};

      return Object.entries(this.modelFieldMap).reduce(
        (agg, [label, value]) => {
          if (value.default != undefined && value.default != null)
            return { ...agg, [label]: value.default };

          return agg;
        },
        {}
      );
    },
    modelName: function () {
      return this.model ? this.model.name : null;
    },
    uploadingFile: function () {
      return false;
    },
    modelFieldMap: function () {
      if (this.model) {
        return this.model.fields.reduce((agg, { label, ...value }) => {
          return { ...agg, [label]: value };
        }, this.values ?? {});
      }

      return null;
    },
  },
  methods: {
    saveRow() {
      const self = this;
      const data = Object.fromEntries(
        Object.entries(this.record).map(function ([key, value]) {
          const fieldType = self.modelFieldMap[key].type;

          if (fieldType == "reference") value = value._id;
          else if (fieldType == "multi-reference")
            value = value.map(({ _id }) => _id);

          return [key, value];
        })
      );

      this.$emit("save", { data });
    },
  },
  components: {
    PierFormFields,
  },
};
</script>