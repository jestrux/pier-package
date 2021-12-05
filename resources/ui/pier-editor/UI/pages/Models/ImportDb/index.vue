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
  <c-box d="flex" justify-content="center">
    <c-box width="500px" my="12" pb="2" border-width="2px" border-color="#444" rounded="lg">
      <c-box>
        <c-box p="6" d="flex" flex-direction="column" align-items="center" justify-content="center">
          <c-box d="flex" align-items="center" justify-content="space-between">
            <c-text color="#ddd" mb="5" fontSize="xl" fontWeight="600">
              Setup Pier Database
            </c-text>
          </c-box>

          <c-box w="full" border-width="2px" border-color="#444" border-style="dashed" rounded="md" 
            d="flex" align-items="center" justify-content="center"
            spacing="5" color="#999"
          >
            <c-box w="full">
              <DropArea 
                placeholder="Drag and drop Pier JSON File here." 
                @selected="handleDbSelected($event)"
              />
            </c-box>
          </c-box>
          <c-stack v-if="models && models.length" w="full" spacing="3" mt="4">
            <c-box v-for="model in models" :key="model.name">
              <model-item 
                :model="model" 
                @change="modelChanged(model.name, $event)"
              />
            </c-box>
          </c-stack>
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
  CSelect,
  CSpinner
} from '@chakra-ui/vue';

import PierEditableModelField from "../components/PierEditableModelField";
import PierModelField from "../components/PierModelField";
import DropArea from "./DropArea";
import { mapState } from 'vuex';
import ModelItem from './ModelItem.vue';

export default {
  name: "ImportDb",
  data() {
    return {
      importedModels: [],
      // models: [],
    };
  },
  computed: {
    ...mapState(['savingModel']),
    canSaveModels(){
      return this.models && this.models.length;
    },
    models(){
      const savedModels = this.importedModels.filter(({status}) => status == "saved").map(({name}) => name);
      return this.importedModels.filter((model) => {
        let referenceFields = model.fields;
        try {
          const parsedFields = JSON.parse(referenceFields);
          referenceFields = parsedFields;
        } catch (error) {
          
        }

        const metaModels = referenceFields.filter(({type}) => ['reference', 'multi-reference'].includes(type)).map(({meta}) => meta.model);
        console.log("Reference fields: ", metaModels);
        return !metaModels.length || metaModels.every(metaModel => savedModels.includes(metaModel));
      });
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
    handleDbSelected(files) {
      if(files && files.length){
        const db = files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
          this.importedModels = JSON.parse(e.target.result).map(m => {
            if(m._id) delete m._id;
            m.status = "";
            return m;
          });
        }
        reader.readAsText(db);
      }
    },
    modelChanged(modelName, status) {
      const models = this.importedModels.map(m => {
        if(m.name == modelName)
          m.status = status;

        return m;
      });

      this.importedModels = models;

      const savedModels = models.filter(({status}) => status == "saved");

      if(savedModels.length == this.importedModels.length)
        this.$store.dispatch("getModels");
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
    CSpinner,
    PierEditableModelField,
    PierModelField,
    DropArea,
    ModelItem
  }
};
</script>