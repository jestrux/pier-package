<template>
  <form action="#" method="POST" @submit.prevent="saveRow">
    <div v-if="model">
      <pier-form-fields
        :key="reloadFields"
        :fields="model.fields"
        :values="record"
      />
    </div>

    <div class="my-7 flex justify-end">
      <button
        type="submit"
        class="w-full px-12 py-0 h-12 bg-primary text-white border-primary text-sm uppercase tracking-wide font-bold focus:outline-none rounded-lg hover:opacity-90"
        :class="{
          'pointer-events-none opacity-50': savingRecord || uploadingFile,
        }"
      >
        {{ savingRecord ? `SAVING ${model.name.toUpperCase()}...` : "SUBMIT" }}
      </button>
    </div>
  </form>
</template>

<script>
import PierFormFields from "../form-fields";
import { handleNetworkError, showSuccessToast } from "../Utils";
import * as API from "../API";
const { insertRecord, updateRecord } = API;

export default {
  name: "PierForm",
  mounted() {
    if (this.values) {
      this.record = this.values;
      this.reloadFields = "be best";
    }

    this.$el.classList.add("PierFormWrapper");

    this.$el.addEventListener("autosave-reference-field", ({ detail } = {}) => {
      if (this.modelName && this.values && this.values._id) {
        API.updateRecord(this.modelName, {
          ...detail,
          _id: this.values._id,
        });
      }
    });

    this.$el.addEventListener("update-form-values", ({ detail } = {}) => {
      this.record = detail;
      this.reloadFields = detail ? detail._id : null;
    });
  },
  data: function () {
    return {
      reloadFields: "be",
      record: {},
      savingRecord: false,
    };
  },
  inject: ["model", "values", "PierCMSConfig"],
  provide() {
    return {
      API,
    };
  },
  computed: {
    modelName() {
      if (this.model) return this.model.name;

      return "";
    },
    uploadingFile: function () {
      return false;
    },
    modelFieldsTypes: function () {
      if (this.model)
        return this.model.fields.reduce((agg, { label, type }) => {
          return { ...agg, [label]: type };
        }, {});
      return null;
    },
  },
  methods: {
    async saveRow() {
      const self = this;
      const data = Object.fromEntries(
        Object.entries(this.record).map(function ([key, value]) {
          const fieldType = self.modelFieldsTypes[key];
          if (fieldType == "auth") value = self.PierCMSConfig.authUser;
          else if (fieldType == "reference") value = value._id;
          else if (fieldType == "multi-reference")
            value = value.map(({ _id }) => _id);

          return [key, value];
        })
      );

      try {
        this.savingRecord = true;
        const record = this.values
          ? await updateRecord(this.modelName, data)
          : await insertRecord(this.modelName, data);
        this.savingRecord = false;

        // document.dispatchEvent(
        //   new CustomEvent("pier-form-success", {
        //     detail: {
        //       record,
        //       el: this.$el,
        //     },
        //   })
        // );

        if (this.PierCMSConfig.onPierFormSuccess) {
          try {
            const onSave = eval(this.PierCMSConfig.onPierFormSuccess);
            onSave.apply(null, [record, this.$el]);

            console.log("Onsave: ", onSave);
            return;
          } catch (error) {
            console.log("onPierFormSuccess error: ", error);
          }
        }

        showSuccessToast(
          this.PierCMSConfig.successMessage ?? `${this.modelName} saved`
        );
        if (
          this.PierCMSConfig.pierRedirectTo &&
          this.PierCMSConfig.pierRedirectTo.length
        ) {
          this.PierCMSConfig.location.href = this.PierCMSConfig.pierRedirectTo;
        }
      } catch (error) {
        handleNetworkError(error, `Error saving ${this.modelName}:`);
        this.savingRecord = false;
      }
    },
  },
  components: {
    PierFormFields,
  },
};
</script>