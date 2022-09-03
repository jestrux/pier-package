<style scoped>
  .popover-custom{
    width: 245px;
    right: 0 !important; left: auto !important;
    background: #fff;
    border: 1px solid #ddd;
  }
  
  .popover-custom select{
    background: #fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='%23999'><polygon points='0,0 100,0 50,50'/></svg>") no-repeat scroll 96.5% 60%;
    background-size: 12px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border: none !important;
  }
</style>
<template>
  <div class="h-screen flex-1 flex flex-col relative">
    <header id="mainNav">
      <span id="pageTitle" class="mr-3">
        {{ modelName }}
      </span>

      <router-link :to="`/${modelName}/list/add`" class="rounded-btn mt-0 ml-3 flex items-center font-bold tracking-widest border bg-blue-700 text-white border-blue-700">
        <svg style="margin-bottom: 0.15rem;" class="mr-1" fill="currentColor" height="20" width="20" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        New {{ modelName }}
      </router-link>

      <span class="flex-1"></span>

      <div class="mr-8 flex items-center">
        <div class="relative rounded-full">
          <svg class="absolute inset-y-0 ml-3 my-auto inset-left-0 w-6 h-6" fill="#aaa">
            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
          </svg>

          <input class="rounded-full py-1 pl-10 pr-3 border border-gray-500 outline-none" 
            style="min-width: 250px" type="search" 
            :placeholder="`Search ${modelName}`" 
            :value="modelFilters.q"
            @input="setFilter({'q': $event.target.value})"
          />
        </div>

        <div class="relative ml-5">
          <button ref="showFiltersButton" id="showFiltersButton" style="display: flex" class="rounded-md border-2 py-1 px-2 flex items-center focus:outline-none" @click="showFilters=!showFilters;">
            <svg fill="#888" width="24" height="24" viewBox="0 0 24 24"><path d="M7,6h10l-5.01,6.3L7,6z M4.25,5.61C6.27,8.2,10,13,10,13v6c0,0.55,0.45,1,1,1h2c0.55,0,1-0.45,1-1v-6 c0,0,3.72-4.8,5.74-7.39C20.25,4.95,19.78,4,18.95,4H5.04C4.21,4,3.74,4.95,4.25,5.61z"/></svg>

            <svg class="h-4 w-4 ml-1" viewBox="0 0 24 24"><path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z"/></svg>
          </button>

          <div id="filterDropdown" ref="filterDropdown" v-show="showFilters" class="popover-custom shadow-md p-3">
            <form action="">
              <div class="input-group">
                <label class="capitalize">
                  Items Per Page
                </label>

                <select :value="modelFilters.perPage" @input="setFilter({'perPage': $event.target.value})">
                  <option :value="5">5</option>
                  <option :value="15">15</option>
                  <option :value="25">25</option>
                  <option :value="40">35</option>
                </select>
              </div>
            </form>
          </div>
        </div>
      </div>
    </header>
        
    <div id="mainContent">
      <ModelRecordList :model="selectedModel" />
    </div>

    <router-view />
  </div>
</template>

<script>
  import Popper from 'popper.js';
  import { mapState, mapGetters } from 'vuex';
  import ModelRecordList from './List';

  Popper.Defaults.modifiers.computeStyle.gpuAcceleration = false;

  export default {
    name: 'PierCMS',
    props: {
      modelName: {
        type: String,
        required: true
      }
    },
    mounted(){
      this.setupModel();

      this.$nextTick(function() {
        this.popper = new Popper(this.$refs["showFiltersButton"], this.$refs["filterDropdown"], {
          // placement: 'bottom-left'
        });
      });

      document.addEventListener("click", (e) => {
        const el = e.target;
        const withinTrigger = el.closest("#showFiltersButton");
        const withinDropdown = el.closest("#filterDropdown");

        if(!withinTrigger && !withinDropdown)
          this.showFilters = false;
      });
      
      document.addEventListener("keyup", (e) => {
        if(e.keyCode === 27)
          this.showFilters = false;
      });
    },
    data() {
      return {
        model: {},
        popper: null,
        showFilters: false
      };
    },
    computed: {
      ...mapState(['modelFilters']),
      ...mapGetters(['selectedModel'])
    },
    watch: {
      modelName: function(){
        this.setupModel();
      },
    },
    methods: {
      setupModel(){
        this.setFilter({'q': ""});
        this.setFilter({'perPage': 25});

        if(!this.modelName)
          return;
        this.$store.dispatch('setSelectedModel', this.modelName);
      },
      setFilter(payload){
        this.$store.dispatch('setFilter', payload);
        this.$nextTick(function() {
          this.$store.dispatch('fetchRecords');
        });
      }
    },
    components: {
      ModelRecordList
    }
  }
</script>