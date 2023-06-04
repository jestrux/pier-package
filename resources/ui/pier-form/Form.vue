<template>
  <form action="#" method="POST" @submit.prevent="saveRow">
    <div :key="reloadFields" class="grid grid-cols-12 gap-5">
        <template
          v-for="field in fields"
        >
          <div :key="field.label" v-if="field.type == 'group'" class="col-span-12 border-b border-neutral-500 mt-4">
            <h3 class="font-bold mb-2 text-xl leading-none">
              {{field.cleanLabel ? field.cleanLabel : field.label}}
            </h3>
          </div>

          <div v-else :key="field.label" class="grid grid-cols-12" :class="{
            'col-span-12': !field.width || field.width == 'full',
            'col-span-6': field.width == 'half',
            'col-span-4': field.width == 'third',
          }">
            <div :class="{
              'col-span-12': !field.stretch || field.stretch == 'full',
              'col-span-6': field.stretch == 'half',
              'col-span-4': field.stretch == 'third',
            }">
              <PierEditorField :field="field" v-model="record[field.label]" />
            </div>
          </div>
        </template>
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
import PierEditorField from "../form-fields/PierEditorField.vue";
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
  inject: ["model", "values"],
  provide() {
    return {
      API,
    };
  },
  computed: {
    // ...mapState(["savingRecord", "model.name"]),
    // ...mapGetters(["model"]),
    fields() {
      if (!this.model) return []

      return this.model.fields.reduce((agg, field, index) => {
        const newGroup = field.group && this.model.fields[index-1]?.group != field.group;

        return [
          ...agg,
          ...(newGroup ? [{ type: "group", label: field.group }] : []),
          field
        ]
      }, []);
    },
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

        showSuccessToast(`${this.modelName} created`);
        if(typeof window.onPierFormSuccess == "function")
          window.onPierFormSuccess();
        else if (window.pierRedirectTo && window.pierRedirectTo.length)
          window.location.href = pierRedirectTo;
      } catch (error) {
        if(typeof window.onPierFormError == "function")
          window.onPierFormError(error, `Error creating ${this.modelName}:`);
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
        if (window.pierRedirectTo && window.pierRedirectTo.length)
          window.location.href = pierRedirectTo;
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

      console.log("Data: ", data);
      if (this.values) this.editRecord(data);
      else this.createRecord(data);
    },
  },
  components: {
    PierEditorField,
  },
};
</script>