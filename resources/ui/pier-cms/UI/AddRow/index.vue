<template>
  <div class="modal open">
    <div class="modal-content" style="width: 570px">
      <div class="modal-title">
        <h3 class="title">New {{ selectedModelName }}</h3>
      </div>

      <form action="#" method="POST"
        @submit.prevent="saveRow"
      >
        <div :key="reloadFields" class="modal-body overflow-y-auto"
          style="padding-top: 10px; padding-bottom: 17px; max-height: 480px;"
        >
          <template v-if="selectedModel">
            <PierEditorField
              v-for="field in selectedModel.fields"
              :key="field.label"
              :field="field"
              v-model="record[field.label]"
            />
          </template>
        </div>

        <div class="modal-buttons">
          <button class="p-2 text-sm"
            :class="{'pointer-events-none opacity-50' : savingRecord || uploadingFile}"
            type="reset"
            @click="$router.replace(`/${selectedModelName}`)"
          >
            CANCEL
          </button>

          <button class="text-sm bg-blue-800 text-white py-2 px-4 rounded"
            :class="{'pointer-events-none opacity-50' : savingRecord || uploadingFile}"
            type="submit"
          >
            {{ savingRecord ? `SAVING ${selectedModelName.toUpperCase()}...` : 'SAVE CHANGES' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import PierEditorField from "./PierEditorField";

export default {
  name: "PierCMSAddRow",
  props: {
    values: Object
  },
  mounted() {
    if (this.values){
      this.record = this.values;
      this.reloadFields = "be best";
    }
  },
  data: function(){
    return {
      reloadFields: "be",
      record: {

      }
    }
  },
  computed: {
    ...mapState(["savingRecord", "selectedModelName"]),
    ...mapGetters(["selectedModel"]),
    uploadingFile: function(){
      return false;
    },
    modelFieldsTypes: function(){
      if(this.selectedModel)
        return this.selectedModel.fields.reduce((agg, {label, type}) => {
          return {...agg, [label]: type};
        }, {});
      return null;
    },
  },
  methods: {
    saveRow() {
      const self = this;
      const data = Object.fromEntries(Object.entries(this.record).map(function([key, value]){
        const fieldType = self.modelFieldsTypes[key];
        if(fieldType == "reference")
          value = value._id;
        else if(fieldType == "multi-reference")
          value = value.map(({_id}) => _id);

        return [key, value];
      }));

      if(this.values)
        this.$store.dispatch("editRecord", data);
      else
        this.$store.dispatch("createRecord", data);
    }
  },
  components: {
    PierEditorField
  }
};
</script>