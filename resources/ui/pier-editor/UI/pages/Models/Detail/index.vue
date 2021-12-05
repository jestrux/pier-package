<template>
  <c-box padding="10" justify-content="center">
    <c-text mb="1" fontSize="4xl" fontWeight="bold">
      {{ model.name }}
    </c-text>

    <c-box d="flex" v-if="!model || !model._id || fetchingModels" padding="6" align-items="center" justify-content="center">
      <!-- <c-circular-progress is-indeterminate /> -->
    </c-box>

    <c-tabs variant-color="orange" :defaultIndex="0" v-else>
      <c-tab-list>
        <c-tab>Structure</c-tab>
        <c-tab>Settings</c-tab>
      </c-tab-list>

      <c-box height="2rem"/>

      <c-tab-panels>
        <c-tab-panel>
          <Structure :model="modelBeingEdited" />
        </c-tab-panel>
        <c-tab-panel>
          <Settings />
        </c-tab-panel>
      </c-tab-panels>
    </c-tabs>
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
} from '@chakra-ui/vue';

import { mapState, mapGetters } from 'vuex';

import dbFieldTypes from '../DbFieldTypes';

import Structure from "./Structure";
import Settings from "./Settings";

export default {
  name: "ModelDetail",
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
  beforeDestroy(){
    // this.$store.dispatch('setModelBeingEdited', null);
  },
  data() {
    return {
    };
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
    }
  },
  watch: {
    models: function(models){
      if(models && !this.modelBeingEdited){
        this.$router.replace('/models');
      }
    },
    modelId: function(modelId){
      console.log("Model being edited: ", modelId);
      this.$store.dispatch('setModelBeingEdited', modelId);
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
    Structure,
    Settings
  }
};
</script>