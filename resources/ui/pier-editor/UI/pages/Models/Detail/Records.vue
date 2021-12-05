<template>
    <c-box p="6" border-width="2px" border-color="#444" rounded="lg">
        <c-box d="flex" v-if="!model || !model._id || fetchingModels || populating || fetchingRecords" padding="6" align-items="center" justify-content="center">
            <c-circular-progress is-indeterminate />
        </c-box>
        
        <c-box v-else-if="!records.length" 
            padding="6" d="flex" flex-direction="column" 
            align-items="center" justify-content="center">
            
            No records found.

            <c-button @click="populate">
            Populate
            </c-button>
        </c-box>
        
        <c-box d="flex" v-else padding="6" align-items="center" justify-content="center">
            {{ records.length }} entries.
        </c-box>
    </c-box>
</template>

<script>

import { 
  CBox, 
  CButton,
  CStack,
  CCircularProgress, CCircularProgressLabel,
} from '@chakra-ui/vue';

import { mapState } from 'vuex';
import { populateModel, browseModel } from '../../../../services/API';

export default {
  name: "ModelRecords",
  props: {
    model: {
      type: Object,
      default: function(){
          return {}
      }
    }
  },
  data() {
    return {
      records: [],
      populating: false,
      fetchingRecords: false,
    };
  },
  computed: {
    ...mapState(['models', 'fetchingModels'])
  },
  watch: {
    model: function(model){
      if(!model)
        return;

      this.fetchRecords();
    }
  },
  methods: {
    async fetchRecords(){
      this.fetchingRecords = true;
      const res = await browseModel(this.model.name);
      this.records = res;
      this.fetchingRecords = false;
    },
    async populate(){
      this.populating = true;
      const res = await populateModel(this.model.name);
      this.records = res;
      this.populating = false;
    },
  },
  components: {
    CBox,
    CButton,
    CStack,
    CCircularProgress, CCircularProgressLabel
  }
};
</script>