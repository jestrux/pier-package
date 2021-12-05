<style>
  #PierCMSList.card .PierPagination{
    background: transparent;
  }

  .PierPagination li a{
    display: inline-block;
    padding: 0.25rem 0.9rem;
    border: 1px solid transparent;
    border-radius: 50%;
    font-weight: bold;
    outline: none;
  }

  .PierPagination li.active a{
    pointer-events: none;
    background: #9a9c9f;
    color: #fff;
  }

  .PierPagination li:not(:first-child):not(:last-child).disabled a{
    padding: 0.25rem;
    margin: 0;
    border: none;
  }
  
  #PierCMSList.card .PierPagination li:first-child,
  #PierCMSList.card .PierPagination li:last-child{
    flex: 1;
    display: flex;
    margin: 0;
  }
  
  .PierPagination li:first-child a,
  .PierPagination li:last-child a{
    border-color: #555;
  }
  
  .PierPagination li:last-child a{
    margin-left: auto;
  }
</style>
<template>
  <div v-if="!records || !records.length || fetchingRecorsds || populatingRecords" class="text-center">
    <Loader v-if="populatingRecords || fetchingRecorsds || !records" :size="90" />

    <div class="py-3" v-else>
      <p class="block mb-5 text-lg">
        This model doesn't contain any records yet.
      </p>

      <button class="mb-2 mr-2 rounded-btn border border-blue-800 capitalize text-blue-800 text-base mt-0 ml-3"
        @click="populateRecords(30)">
        Populate <span class="font-bold">30</span> sample records
      </button>

      <button class="mb-2 rounded-btn border border-blue-800 capitalize text-blue-800 text-base mt-0 ml-3"
        @click="populateRecords(50)">
        Populate <span class="font-bold">50</span> sample records
      </button>
    </div>
  </div>

  <div v-else>
    <div class="grid grid-gap-2 -mx-3"  style="grid-template-columns: 1fr 1fr 1fr">
      <div class="p-3" v-for="record in records" :key="record._id">
        <component :is="cardComponent" :values="bindValues(record)" 
        />
      </div>
    </div>

    <paginate v-if="records && records.length && !populatingRecords && !fetchingRecorsds && recordsPagination"
      :value="recordsPagination.current_page"
      :page-count="recordsPagination.last_page"
      :click-handler="fetchRecords"
      :prev-text="'Prev'"
      :next-text="'Next'"
      container-class="PierPagination mt-8 py-2 flex items-center" 
    />
  </div>
  
</template>

<script>
  import Paginate from 'vuejs-paginate';
  import { populateModel } from "../../../API";
  import { mapState } from 'vuex';

  export default {
    name: 'PierCMSListCard',
    props: {
      model: {
        type: Object,
        default: function(){
          return {}
        }
      }
    },
    mounted(){
      this.fetchRecords();
    },
    computed: {
      ...mapState(['fetchingRecorsds', 'records', 'recordsPagination', 'populatingRecords']),
      cardComponent() {
        return () => import(`./CardOptions/${this.model.settings.listPageCard.card}.vue`)
      }
    },
    watch: {
      model: function(newValue){
        if(newValue)
          this.fetchRecords();
      },
    },
    methods: {
      fetchRecords(page = 1){
        if(!this.model.fields)
          return;
        
        this.$store.dispatch('fetchRecords', page);
      },
      bindValues(record){
        if(!this.model || !this.model.settings)
          return {};

        const bindings = this.model.settings.listPageCard.bindings;
        const values = {};
        Object.keys(bindings).forEach(key => {
          values[key] = record[bindings[key]];
        });;

        return values;
      },
      populateRecords(itemCount){
        this.$store.dispatch('populateRecords', itemCount);
      },
    },
    components:{
      Paginate
    }
  }
</script>