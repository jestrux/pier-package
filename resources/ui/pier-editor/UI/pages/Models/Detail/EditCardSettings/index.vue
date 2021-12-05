<style>
    [data-chakra-component="CModalContent"] > section{
      max-width: 1000px;
      max-height: 650px;
      background: #303030;
      border-radius: 8px;
    }

    [data-chakra-component="CModalBody"]{
      border: 1px solid #4b4b4b;
      border-width: 2px 0;
      padding-top: 1rem;
      padding-bottom: 1rem;
      background-color: #424242;
    }
</style>
<template>
    <div id="EditCardSettings">
        <c-modal
          size="xl"
          :is-open="isOpen"
          :block-scroll-on-mount="true"
          isCentered
          scroll-behavior="inside"
          initialFocusRef="content"
        >
        <c-modal-content ref="content">
            <c-modal-header display="flex" alignItems="center" justifyContent="space-between">
              <c-text textTransform="capitalize" fontSize="2xl">
                {{ modalTitle }}
              </c-text>
              <c-modal-close-button position="relative" top="0" right="-0.5rem" 
                :disabled="modelBeingEdited && modelBeingEdited.savingSettings"
                @click="close"
              />
            </c-modal-header>
            <c-modal-body>

              <PickCard v-if="currentPage === 'pick-card-template'" v-model="selectedCard" />

              <CustomizeCard v-else :selected-card="selectedCard" v-model="cardDetails" />

            </c-modal-body>
            <c-modal-footer>
                <c-button w="32" mr="3" 
                  :disabled="modelBeingEdited && modelBeingEdited.savingSettings"
                  @click="close"
                >
                  Cancel
                </c-button>
                <c-button w="32" variant-color="orange" 
                  :disabled="disableSubmit"
                  :is-loading="modelBeingEdited && modelBeingEdited.savingSettings"
                  @click="handleSubmitClicked"
                >
                  {{ submitText }}
                </c-button>
            </c-modal-footer>
        </c-modal-content>
        <c-modal-overlay />
        </c-modal>
    </div>
</template>

<script>

import {
  CModal,
  CModalOverlay,
  CModalContent,
  CModalHeader,
  CModalFooter,
  CModalBody,
  CModalCloseButton,
  CBox,
  CText,
  CButton
} from '@chakra-ui/vue';

import { mapState, mapGetters } from 'vuex';
import PickCard from "./PickCard";
import CustomizeCard from "./CustomizeCard";

export default {
  name: "EditCardSettings",
  data() {
    return {
      isOpen: true,
      currentPage: 'pick-card-template',
      // currentPage: 'customize',
      selectedCard: null,
      // selectedCard: 'TaskCard',
      cardDetails: {}
    };
  },
  methods: {
    open() {
      this.isOpen = true
    },
    close() {
      // this.isOpen = false
      this.$emit('close');
    },
    bindSettings() {
      if(!model) return;
        
      this.currentPage = 'pick-card-template';

      const settings = model.settings;
      if(settings && settings.listPageCard)
        this.selectedCard = settings.listPageCard.card;
      else
        this.selectedCard = null;
    },
    handleSubmitClicked() {
      if(this.currentPage === 'pick-card-template')
        this.currentPage = 'customize-card';
      else{
        const { card, bindings } = this.cardDetails;
        const settings = {
          listPageType: "card",
          listPageCard: {
            card,
            bindings
          }
        };

        this.$store.dispatch('updateModelSettings', settings);

        this.$store.subscribe(({type, payload}) => {
          if(type === 'UPDATE_MODEL_DETAILS'){
            if(payload.updatedDetails && payload.updatedDetails.settings)
              this.close();
          }
        });
      }
    }
  },
  computed: {
    ...mapState(['models', 'fetchingModels']),
    ...mapGetters(['modelBeingEdited']),
    modalTitle(){
      return this.currentPage === 'pick-card-template' ? 'Pick Card Template' : `Customize ${this.selectedCard} Card`;
    },
    submitText(){
      return this.currentPage === 'pick-card-template' ? "Continue" : "Submit";
    },
    disableSubmit(){
      return this.currentPage === 'pick-card-template' ? !this.selectedCard : false;
    }
  },
  watch: {
    isOpen: {
      immediate: true,
      handler(){
        this.bindSettings();
      }
    },
    modelBeingEdited: {
      immediate: true,
      handler(model){
        this.bindSettings();
      }
    }
  },
  components: {
    CBox,
    CText,
    CModal,
    CModalOverlay,
    CModalContent,
    CModalHeader,
    CModalFooter,
    CModalBody,
    CModalCloseButton,
    CButton,
    PickCard,
    CustomizeCard
  }
};
</script>