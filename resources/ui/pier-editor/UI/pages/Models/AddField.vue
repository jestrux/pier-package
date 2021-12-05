<template>
  <c-box d="flex" padding="10" flex-direction="column" align-items="center" justify-content="center">
    <c-box width="780px" mb="6" pb="2" border-width="2px" border-color="#444" rounded="lg">
      <c-box>
        <c-box p="6">
          <c-box d="flex" align-items="center" justify-content="space-between">
            <c-text color="#ddd" mb="2" fontSize="2xl">
              Add Field {{ model ? `to ${model.name}` : '' }}
            </c-text>

            <c-stack isInline spacing="3">
              <c-button size="md" align-self="flex-start" variant="outline" color="#777" 
                @click="$router.replace(`/models${model ? '/' + model._id + '/details' : ''}`)">
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
                  Save Field
                </c-box>
              </c-button>
            </c-stack>
          </c-box>

          <c-box mt="6" mb="8">
            <PierEditableModelField
              :selectable="true"
              :selected="true"
              v-model="field"
            />
          </c-box>

          <c-box d="flex" spacing="5" color="#999">
            <c-form-control flex="1">
              <c-form-label fontSize="xl" htmlFor="modelName">Place Field</c-form-label>
              <c-select size="lg" v-model="newFieldPlacement">
                <option value="start">Before all fields</option>
                <option value="end">At the end</option>
                <option value="after">After another field</option>
              </c-select>
            </c-form-control>
            <c-box w="6"></c-box>
            <c-form-control flex="1">
              <template v-if="newFieldPlacement == 'after'">
                <c-form-label fontSize="xl" htmlFor="modelName">Choose Other Field</c-form-label>
                <c-select size="lg" v-model="newFieldPlacementAfter">
                  <template v-for="(field, index) in model.fields">
                    <option :key="index" :value="field.label">{{field.label}}</option>
                  </template>
                </c-select>
              </template>
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
  CText,
  CTabs,
  CTabList,
  CTab,
  CTabPanels,
  CTabPanel,
  CCircularProgress, CCircularProgressLabel,
  CButton,
  CIcon,
  CStack,
  CFormLabel,
  CFormControl,
  CInput,
  CSelect
} from '@chakra-ui/vue';


import dbFieldTypes from './DbFieldTypes';

import PierEditableModelField from "./components/PierEditableModelField";
import PierModelField from "./components/PierModelField";

import { mapState, mapGetters } from 'vuex';
import { saveNewModelField } from '../../../services/API';

export default {
  name: "ModelsAddField",
  props: {
    modelId: {
      type: String,
      required: true
    }
  },
  mounted(){
    if(!this.models && !this.fetchingModels){
      this.$store.dispatch('getModels');
      this.$store.subscribe(mutation => {
        if(mutation.type === 'SET_MODELS')
          this.$store.dispatch('setModelBeingEdited', this.modelId);
      });
    }
    else
      this.$store.dispatch('setModelBeingEdited', this.modelId);
  },
  // beforeDestroy(){
  //   this.$store.dispatch('setModelBeingEdited', null);
  // },
  data() {
    return {
      field: {},
      newFieldPlacement: 'end',
      newFieldPlacementAfter: '',
      savingModel: false
    };
  },
  methods: {
    async saveModel() {
      const field = this.field;

      let fieldDetails = {
        ...field,
        type: field.type.value,
      };

      const fieldOptions = {...field.type.options};
      
      delete fieldDetails.defaultValue;

      let fieldMeta = {};
      for (let [key, option] of Object.entries(fieldOptions)) {
        if(option.value !== undefined)
          fieldMeta[key] = option.value;

        if(option.meta !== undefined && Object.keys(option.meta).length)
          fieldMeta = {...fieldMeta, ...option.meta};
      }

      if(Object.keys(fieldOptions).length)
        fieldDetails.meta = fieldMeta;

      const data = {
        field: fieldDetails,
        placement: this.newFieldPlacement
      };

      if(this.newFieldPlacement == 'after')
        data.after = this.newFieldPlacementAfter;

      const res = await saveNewModelField(this.modelBeingEdited.name, data);
      
      this.$store.dispatch('updateModelBeingEditedDetails', {
        fields: JSON.parse(res.fields)
      });

      this.$router.replace(`/models/${res._id}/details`);
    }
  },
  computed: {
    ...mapState(['models', 'fetchingModels']),
    ...mapGetters(['modelBeingEdited']),
    model(){
      if(!this.modelBeingEdited)
        return {};

      let typeMap = {
        status: 'Status',
        reference: "Reference",
        'multi-reference': "Multi Reference"
      };

      dbFieldTypes.forEach(({label, value}) => {
        typeMap[value] = label;
      });

      const fields = this.modelBeingEdited.fields.map(field => {
        field.typeLabel = typeMap[field.type];
        return field;
      });

      return {
        ...this.modelBeingEdited,
        fields
      };
    },
    canSaveModel(){
      let placementDetailsFilled = true;

      if(this.newFieldPlacementAfter == "after" && (!this.newFieldPlacementAfter || !this.newFieldPlacementAfter.length))
        placementDetailsFilled = false;
        
      return this.field && this.field.label && this.field.type && placementDetailsFilled;
    },
  },
  watch: {
    models: function(models){
      if(models && !this.modelBeingEdited){
        this.$router.replace('/models');
      }
    },
    modelId: function(modelId){
      this.$store.dispatch('setModelBeingEdited', modelId);
    },
    modelBeingEdited: function(model){
      this.newFieldPlacementAfter = model.fields[0].label;
    }
  },
  components: {
    CBox,
    CText,
    CTabs,
    CTabList,
    CTab,
    CTabPanels,
    CTabPanel,
    CCircularProgress, CCircularProgressLabel,
    CButton, 
    CIcon, 
    CStack,
    CFormLabel,
    CFormControl,
    CInput,
    CSelect,
    PierEditableModelField,
    PierModelField
  }
};
</script>