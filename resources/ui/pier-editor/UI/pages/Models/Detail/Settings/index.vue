<style scoped>
    .PierSettingsOption{
      padding: 0.6rem 0.8rem;
      position: relative;
      text-align: left;
      background: transparent;
      outline: none !important;
      box-shadow: none !important;
      border: 4px solid transparent;
      border-radius: 10px;
      overflow: hidden;
      cursor: pointer;
      background: #f0f0f0;
    }

    .PierSettingsOption img{
      height: 130px;
    }

    .PierSettingsOption:not(.selected):hover{
      opacity: 0.85;
    }

    .PierSettingsOption.selected{
      border-color: #ffba7f;
      pointer-events: none;
    }

    .PierSettingsOption:before{
      background: #ffba7f;
      width: 160px;
      height: 80px;
      position: absolute;
      right: -70px;
      top: -30px;
      transform: rotate(45deg);
      border-radius: 0 0 90px 90px;
    }
    
    .PierSettingsOption:after{
      color: #fff;
      position: absolute;
      right: 5px;
      top: 5px;
      width: 30px;
      height: 30px;
      background: url("data:image/svg+xml;utf8, <svg xmlns='http://www.w3.org/2000/svg' width='26' height='26' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'><polyline points='20 6 9 17 4 12'></polyline></svg>") no-repeat;
    }

    .PierSettingsOption.selected:before,
    .PierSettingsOption.selected:after{
      content: "";
    }

    .PierSettingsOption > .PierCard{
      margin: -4px 0;
      border-radius: 0 !important;
    }
    
    .PierSettingsOption *{
      pointer-events: none !important;
    }
</style>
<template>
    <div>
      <c-box d="flex" alignItems="center" p="6" mb="8" border-width="2px" border-color="#444" rounded="lg">
        <c-box flex="1">
          <c-text fontSize="2xl">Display Field</c-text>    
        </c-box>
        <c-box flex="2">
          <c-box maxW="xs" pos="relative">
            <c-select :is-disabled="modelBeingEdited.savingDisplayField" :value="displayField" @change="displayFieldChanged($event)" placeholder="Choose one">
                <option v-for="(choice, index) in displayFieldChoices"
                  :key="index" :value="choice"
                >
                  {{ choice }}
                </option>
            </c-select>

            <c-box v-if="modelBeingEdited.savingDisplayField" d="flex" alignItems="center" pos="absolute" top="0" bottom="0" right="2">
              <c-spinner size="md" />
            </c-box>
          </c-box>
        </c-box>
      </c-box>

      <c-box d="flex" p="6" border-width="2px" border-color="#444" rounded="lg">
        <c-box flex="1">
          <c-text fontSize="2xl">List Page Style</c-text>    
        </c-box>
        <c-box flex="2">
          <c-box d="grid" gridTemplateColumns="1fr 1fr" gridGap="8">
            <button type="button" class="focus:outline-none"
              @click="setListPageType('table')"
            >
              <div class="PierSettingsOption" :class="{'selected': settings.listPageType === 'table'}">
                <img class="shadow-md rounded w-full object-cover object-left-top" src="pier/images/table.png" alt="">
              </div>

              <c-text :color="settings.listPageType === 'table' ? '#ffba7f' : ''"
                :class="{'opacity-75': settings.listPageType !== 'table'}"
                class="text-center uppercase tracking-widest" fontSize="xl" mt="3">
                Table View
              </c-text>
            </button>

            <button type="button" class="focus:outline-none"
              @click="setListPageType('card')"
            >
              <div class="PierSettingsOption" :class="{'selected': settings.listPageType === 'card'}"
              >
                <img class="shadow-md rounded-md w-full object-cover object-left-top" src="pier/images/card.png" alt="">
              </div>

              <c-text :color="settings.listPageType === 'card' ? '#ffba7f' : ''"
                :class="{'opacity-50': settings.listPageType !== 'card'}"
                class="text-center uppercase tracking-widest" fontSize="xl" mt="3">
                Card View
              </c-text>
            </button>
          </c-box>
        </c-box>

        <EditCardSettings v-if="editCard" @close="editCard = false" />
      </c-box>
    </div>
</template>

<script>

import { 
  CBox, 
  CButton,
  CText,
  CSelect,
  CSpinner,
} from '@chakra-ui/vue';

import { mapGetters } from 'vuex';
import { populateModel, browseModel } from '../../../../../services/API';
import EditCardSettings from "../EditCardSettings";

export default {
  name: "ModelSettings",
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
      settings: {},
      editCard: false,
      displayField: ""
    };
  },
  computed: {
    ...mapGetters(['modelBeingEdited']),
    displayFieldChoices(){
      if(this.modelBeingEdited)
        return this.modelBeingEdited.fields.map(({label}) => label);

      return [];
    }
  },
  watch: {
    modelBeingEdited: {
      immediate: true,
      handler(model){
        if(!model)
          return;

        this.settings = model.settings;
        this.displayField = model.display_field;
      }
    }
  },
  methods: {
    async setListPageType(listPageType){
      if(listPageType === 'table'){
        // this.settings.listPageType = type;
        this.$store.dispatch('updateModelSettings', {...this.settings, listPageType});
      }
      else
        this.editCard = true;
    },
    displayFieldChanged(newValue){
      this.$store.dispatch('updateModelDisplayField', newValue);
    }
  },
  components: {
    CBox,
    CButton,
    CText,
    CSelect,
    CSpinner,
    EditCardSettings
  }
};
</script>