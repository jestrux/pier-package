<style>
  #CustomizeCard{
    margin: -1rem -2rem;
    height: calc(100% + 2rem);
  }

  #customizeFields{
    background: #333;
  }

  #customizeFields [data-chakra-component="CFormControl"]{
    padding: 0.85rem 1rem;
    padding-left: 1.8rem;
  }

  #customizeFields [data-chakra-component="CFormControl"] + [data-chakra-component="CFormControl"]{
    border-top: 1px solid #3f3f3f;
  }
  
  #customizeFields [data-chakra-component="CInput"],
  #customizeFields [data-chakra-component="CSelect"]{
    width: 170px;
  }
</style>
<template>
    <c-box id="CustomizeCard" d="grid" alignItems="center" gridTemplateColumns="1fr 1fr">
      <c-box id="customizeFields" class="h-full overflow-y-auto">
        <c-form-control d="flex" alignItems="center" justifyContent="space-between" py="4" 
          v-for="(option, index) in options" 
          :key="option.type === 'static' ? `${option.value}${index}` : `${option.bindTo}${index}`"
        >
          <c-form-label :html-for="option.label" color="#888" fontSize="lg" mr="1">
            <span v-if="option.type === 'static'" style="color: #ccc">
              {{ option.label }}:
            </span>
            
            <template v-else>
              Bind&nbsp; <span style="color: #ccc">{{ option.label }}</span> &nbsp;to:
            </template>
          </c-form-label>

          <c-input v-if="option.type === 'static'" 
            v-model="option.value" 
            placeholder="Enter here"
          />

          <c-select v-else v-model="option.bindTo" placeholder="Choose field">
            <option v-for="(choice, index) in option.choices" 
              :key="index"
              :value="choice"
            >
              {{ choice }}
            </option>
          </c-select>
        </c-form-control>
      </c-box>

      <div class="flex flex-col justify-center h-full overflow-y-auto px-8 py-5">
        <c-text color="#636363" mb="5" textAlign="center" fontSize="3xl" fontWeight="bold" class="uppercase tracking-wide">
          Card Preview
        </c-text>

        <component v-if="card" :is="card" :values="values" />
      </div>
      
    </c-box>
</template>

<script>

import { 
  CBox, 
  CText,
  CFormControl, 
  CFormLabel, 
  CInput,
  CSelect
} from '@chakra-ui/vue';
import ArticleCard from "../CardOptions/ArticleCard";
import EventCard from "../CardOptions/EventCard";
import ProfileCard from "../CardOptions/ProfileCard";
import TaskCard from "../CardOptions/TaskCard";
import ShopCard from "../CardOptions/ShopCard";
import CourseCard from "../CardOptions/CourseCard";

import CardFieldTypes from "./CardFieldTypes";

import { mapState, mapGetters } from 'vuex';

export default {
  name: "CustomizeCard",
  props: {
    selectedCard: String
  },
  data(){
    return {
      card: "",
      model: null,
      options: []
    }
  },
  mounted(){
    this.bindCard();  
  },
  computed:{
    ...mapGetters(['modelBeingEdited']),
    values(){
      const values = {};
      this.options.forEach(option => {
        if(option.bindTo && option.bindTo.length)
          values[option.key] = option.defaultValue;
      });

      return values;
    }
  },
  watch:{
    selectedCard(){
      if(!this.options || !this.options.length)
        this.bindCard();
    },
    modelBeingEdited(){
      if(!this.model)
        this.bindCard();
    },
    options:{
      deep: true,
      handler: function(){
        const bindings = {};
        this.options.forEach(option => {
          if(option.bindTo && option.bindTo.length)
            bindings[option.key] = option.bindTo;
        });

        this.$emit('input', {card: this.card, bindings});
      }
    }
  },
  methods: {
    bindCard(){
      if(this.modelBeingEdited)
        this.model = JSON.parse(JSON.stringify(this.modelBeingEdited));

      if(!this.selectedCard)
        return;

      const card = CardFieldTypes[this.selectedCard];
      if(card){
        this.card = card.component;
        const options = JSON.parse(JSON.stringify(card.options));

        this.options = Object.keys(options).map(key => {
          const option = {
            key, 
            ...options[key],
            choices: []
          };

          if(option.type === 'static'){
            option.value = option.defaultValue;
            return option;
          }
          
          if(option.accepts === 'multi-reference'){
            // const accepts = option.accepts.split('|').map(item => item.trim());
            // return accepts.includes(type);
          }
          else if(this.model){
            option.choices = this.model.fields.filter(({type}) => {
              const accepts = option.accepts.split(',').map(item => item.trim());
              return accepts.includes(type);
            }).map(({label}) => label);
          }

          if(option.choices.length == 1)
            option.bindTo = option.choices[0];

          return option;
        });
      }
    }
  },
  components: {
    CBox,
    CText,
    CFormControl, 
    CFormLabel, 
    CSelect,
    CInput,
    ArticleCard,
    EventCard,
    ProfileCard,
    TaskCard,
    ShopCard,
    CourseCard,
  }
};
</script>