<template>
  <form action="#" method="POST" @submit.prevent="saveRow">
    <div v-if="model">
      <pier-form-fields :key="reloadFields" :fields="model.fields" :values="record" />
    </div>

    <div class="my-7 flex justify-end">
      <button type="submit" class="w-full px-12 py-0 h-12 bg-primary text-white border-primary text-sm uppercase tracking-wide font-bold focus:outline-none rounded-lg hover:opacity-90"
        :class="{
          'pointer-events-none opacity-50': savingRecord || uploadingFile,
        }"
      >
        {{
          savingRecord
            ? `SAVING ${model.name.toUpperCase()}...`
            : "SUBMIT"
        }}
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
    async createRecord(data) {
      this.savingRecord = true;
      try {
        let record = await insertRecord(this.modelName, data);

        this.savingRecord = false;

        if(typeof this.PierCMSConfig.onPierFormSuccess == "function")
          this.PierCMSConfig.onPierFormSuccess(record, this.$el);
        else {
          showSuccessToast(this.PierCMSConfig.successMessage ?? `${this.modelName} created`);
          if (this.PierCMSConfig.pierRedirectTo && this.PierCMSConfig.pierRedirectTo.length)
            this.PierCMSConfig.location.href = this.PierCMSConfig.pierRedirectTo;
        }

        document.dispatchEvent(
          new CustomEvent("pier-form-success", {
            detail: {
              record, el: this.$el
            },
          })
        );
      } catch (error) {
        if(typeof this.PierCMSConfig.onPierFormError == "function")
          this.PierCMSConfig.onPierFormError(error, `Error creating ${this.modelName}:`);
        else
          handleNetworkError(error, `Error creating ${this.modelName}:`);
        this.savingRecord = false;
      }
    },
    async editRecord(data) {
      this.savingRecord = true;
      try {
        const record = await updateRecord(this.modelName, data);

        this.savingRecord = false;

        showSuccessToast(`${this.modelName} updated`);
        if (this.PierCMSConfig.pierRedirectTo && this.PierCMSConfig.pierRedirectTo.length)
          window.location.href = this.PierCMSConfig.pierRedirectTo;
      } catch (error) {
        handleNetworkError(error, `Error creating ${this.modelName}:`);
        this.savingRecord = false;
      }
    },
    saveRow() {
      const self = this;
      const data = Object.fromEntries(
        Object.entries(this.record).map(function ([key, value]) {
          const fieldType = self.modelFieldsTypes[key];
          if (fieldType == "reference") value = value._id;
          else if (fieldType == "multi-reference")
            value = value.map(({ _id }) => _id);

          return [key, value];
        })
      );

      if (this.values) this.editRecord(data);
      else this.createRecord(data);
    },
  },
  components: {
    PierFormFields,
  },
};
</script>