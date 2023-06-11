<template>
  <form :id="formId" action="#" method="POST" @submit.prevent="saveRow">
    <div
      class="modal-body overflow-y-auto"
      style="padding-top: 10px; padding-bottom: 17px"
      :style="{ maxHeight: onFullPage ? '' : '480px' }"
    >
      <pier-form-fields :fields="model.fields" :values="record" />
    </div>

    <template v-if="!hideActionButtons">
      <div v-if="onFullPage" class="flex justify-end">
        <button
          type="submit"
          class="w-full px-12 py-0 h-12 bg-primary text-white border-primary text-sm uppercase tracking-wide font-bold focus:outline-none rounded-lg hover:opacity-90"
          :class="{
            'pointer-events-none opacity-50': saving || uploadingFile,
          }"
        >
          {{
            saving || uploadingFile
              ? `SAVING ${modelName.toUpperCase()}...`
              : record && record._id
              ? "SAVE CHANGES"
              : "SAVE"
          }}
        </button>
      </div>

      <div
        v-else
        class="flex items-center justify-end bg-black/5 py-2.5 pr-4 gap-2 rounded-b-md"
      >
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
            saving
              ? `SAVING ${modelName.toUpperCase()}...`
              : record && record._id
              ? "SAVE CHANGES"
              : "SAVE"
          }}
        </button>
      </div>
    </template>
  </form>
</template>

<script>
import PierFormFields from "../../../form-fields";

export default {
  name: "AddRowForm",
  props: {
    values: Object,
    model: Object,
    saving: Boolean,
    isPopup: Boolean,
    formId: String,
    hideActionButtons: Boolean,
    onFullPage: Boolean,
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
          console.log("Field map: ", self.modelFieldMap, key);
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