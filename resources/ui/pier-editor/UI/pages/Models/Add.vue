<style>
[data-chakra-component="CInput"]{
  color: #999;
  outline-color: #555;
}

[data-chakra-component="CInput"]:focus{
  border-color: #555;
  box-shadow: 0 0 0 1px #555;
}

[data-chakra-component="CInput"]::placeholder{
  color: #6a6a6a;
}
</style>
<template>
  <c-box d="flex" padding="10" flex-direction="column" align-items="center" justify-content="center">
    <c-box width="780px" mb="6" pb="2" border-width="2px" border-color="#444" rounded="lg">
      <c-box>
        <c-box p="6">
          <c-box d="flex" align-items="center" justify-content="space-between">
            <c-text color="#ddd" mb="5" fontSize="2xl">
              New Pier Model
            </c-text>

            <c-stack isInline spacing="3">
              <c-button size="md" align-self="flex-start" variant="outline" color="#777" 
                @click="$router.replace('/models')">
                <c-icon name="close" size="11px" mr="3" /> Cancel
              </c-button>

              <c-button size="md" 
                :disabled="!canSaveModel"
                px="4" variant-color="orange"
                :isLoading="savingModel"
                @click="saveModel">
                <c-box d="inline-flex" align-items="center">
                  <c-box mr="3" mb="1">
                    <c-icon name="check" size="15px" />
                  </c-box>
                  Save Model
                </c-box>
              </c-button>
            </c-stack>
          </c-box>

          <c-box spacing="5" color="#999">
            <c-form-control>
              <c-form-label fontSize="xl" htmlFor="modelName">Model Name</c-form-label>
              <c-input id="modelName" type="text" placeholder="E.g Article" size="lg" v-model="name" />
              
              <c-form-helper-text id="email-helper-text">
                Pascal case is a more standard way to name model
              </c-form-helper-text>
            </c-form-control>
            
            <c-box d="flex" mb="1" mt="5" align-items="center" justify-content="space-between">
              <c-text fontSize="xl" htmlFor="label">Fields</c-text>

              <c-button v-if="curFieldIndex === -1"
                alignSelf="center" variant="ghost" color="orange.200" @click="addField">
                <c-box d="inline-flex" align-items="center">
                  <c-box mr="2" mb="1">
                    <c-icon name="add" size="14px" />
                  </c-box>
                  Add Field
                </c-box>
              </c-button>
            </c-box>

            <template v-for="(field, index) in fields">
              <PierEditableModelField :key="index"
                v-model="fields[index]"
                :selectable="curFieldIndex === -1 || curFieldIndex === index"
                :selected="curFieldIndex === index"
                :editing="editing"
                @selectField="curFieldIndex = index; editing = true"
                @cancelAddField="cancelAddField"
                @removeField="removeField(index)"
              />
            </template>

            <button v-if="curFieldIndex === -1" 
              style="width: 100%; border: none; background: transparent; outline: none"
              @click="addField">
              <PierModelField
                color="#c09669"
                icon="add"
                label="add field"
              />
            </button>

            <c-form-control class="mt-6" 
              :style="{
                opacity: !fieldsAvailable || curFieldIndex !== -1 ? 0.3 : 1,
                pointerEvents: !fieldsAvailable || curFieldIndex !== -1 ? 'none' : ''
              }">
              <c-form-label fontSize="xl" htmlFor="modelName">Display Field</c-form-label>
              <c-select size="lg" v-model="displayField" placeholder="Choose model display field">
                  <template v-if="fieldsAvailable">
                    <option v-for="(field, index) in fields" 
                        :key="index"
                        :value="field.label"
                    >
                        {{ field.label }}
                    </option>
                  </template>
              </c-select>
            </c-form-control>
          </c-box>
        </c-box>
      </c-box>
    </c-box>
  </c-box>
</template>

<script>

import { 
  CBox,
  CPseudoBox,
  CText, 
  CButton, 
  CIcon, 
  CStack, 
  CFormControl,
  CFormLabel,
  CFormHelperText,
  CInput,
  CSelect
} from '@chakra-ui/vue';

import dbFieldTypes from "./DbFieldTypes";
import PierEditableModelField from "./components/PierEditableModelField";
import PierModelField from "./components/PierModelField";
import { mapState } from 'vuex';

export default {
  name: "ModelsAdd",
  mounted(){
    this.$nextTick(() => {
      this.$el.querySelector("#modelName").focus();
    })
  },
  data() {
    return {
      name: "",
      editing: false,
      dbFieldTypes,
      curFieldIndex: -1,
      fields: [],
      displayField: null
    };
  },
  computed: {
    ...mapState(['savingModel']),
    canSaveModel(){
      return this.name.length > 0 && this.curFieldIndex === -1 && this.displayField;
    },
    fieldsAvailable(){
      const fields = this.fields;

      if(!fields || !fields.length)
        return false;

      const validFields = fields.filter(({label}) => label && label.length);
      return validFields.length > 0;
    }
  },
  watch: {
    curFieldIndex(curFieldIndex){
      if(curFieldIndex !== -1 && this.displayField){
        if(this.fields[curFieldIndex].label === this.displayField)
          this.displayField = null;
      }
    }
  },
  methods: {
    removeField(index) {
      if(this.fields[index] && this.fields[index].label === this.displayField)
        this.displayField = null;

      this.fields.splice(index, 1);
      this.editing = false;
      this.curFieldIndex = -1;
    },
    cancelAddField() {
      this.editing = false;
      this.curFieldIndex = -1;
    },
    saveModel() {
      const data = {
        name: this.name.replace(/ /g,""),
        displayField: this.displayField,
        fields: this.fields.map(field => {
          let fieldDetails = {
            ...field,
            type: field.type.value,
          };

          delete fieldDetails.defaultValue;

          const fieldOptions = {...field.type.options};
          let fieldMeta = {};
          for (let [key, option] of Object.entries(fieldOptions)) {
            if(option.value !== undefined)
              fieldMeta[key] = option.value;

            if(option.meta !== undefined && Object.keys(option.meta).length)
              fieldMeta = {...fieldMeta, ...option.meta};
          }

          if(Object.keys(fieldOptions).length)
            fieldDetails.meta = fieldMeta;

          return fieldDetails;
        })
      }

      // console.log("Model: ", data);
      this.$store.dispatch('createModel', data);
    },
    addField() {
      this.editing = false;
      this.fields.push({
        label: "",
        type: {}
      });

      this.curFieldIndex = this.fields.length - 1;
    }
  },
  components: {
    CBox, 
    CPseudoBox, 
    CText, 
    CButton, 
    CIcon, 
    CStack, 
    CFormControl,
    CFormLabel,
    CFormHelperText,
    CInput,
    CSelect,
    PierEditableModelField,
    PierModelField
  }
};
</script>