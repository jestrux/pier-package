<template>
  <div v-if="models">
    <ImportDb v-if="!models.length" />

    <div v-else class="h-screen w-full flex items-center justify-center">
      <c-text fontSize="4xl" color="#444">
        Pick a model to edit or add a new one.
      </c-text>
    </div>
  </div>
</template>

<script>

import { CBox, CText, CButton, CIcon, CLink, CStack, 
  CCircularProgress, CCircularProgressLabel 
} from '@chakra-ui/vue';
import { mapState } from 'vuex';
import ImportDb from './ImportDb';

export default {
  name: "ModelsList",
  mounted() {
    if(!this.models)
        this.fetchModels();
  },
  data() {
    return {};
  },
  computed: {
    ...mapState(['models', 'fetchingModels'])
  },
  methods: {
    fetchModels(page = 1){
      this.$store.dispatch('getModels', page);
    },
    deleteModel(modelId) {
      this.$store.dispatch('removeModel', modelId);
    },
  },
  components: {
    CBox, CText, CButton, CIcon, CLink, CStack, 
    CCircularProgress, CCircularProgressLabel,
    ImportDb
  }
};
</script>