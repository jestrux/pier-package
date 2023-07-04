<style scoped>
  .pier-th.image,
  .pier-th.phone,
  .pier-th.email,
  .pier-th.video,
  .pier-th.rating,
  .pier-th.location,
  .pier-th.boolean,
  .pier-th.status,
  .pier-th.color,
  .pier-th.date{
    text-align: center;
  }
</style>
<template>
  <div class="PierTable">
    <table class="pure-table bg-white pure-table-striped" 
      style="border-top: none !important; width:100%; min-width: 500px;">
      <thead>
        <tr>
          <template v-for="(field, index) in model.fields">
            <th class="capitalize" :class="['pier-th', field.type, field.meta ? field.meta.type : '']" 
              v-if="field.type !== 'password'"
              :key="index">
              {{ field.label.replace(/_/g, ' ') }}
            </th>
          </template>
          <th style="width: 120px" class="text-center">
            Actions
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-if="records && records.length && !fetchingRecorsds && !populatingRecords">
          <TableRow v-for="record in records"
            :key="record._id"
            :fields="model.fields"
            :data="record"
          />
        </template>
        <tr v-else>
          <td :colspan="model.fields.length + 1"
            class="text-center">

            <Loader v-if="!records || fetchingRecorsds || populatingRecords" :size="90" />

            <div class="py-3" v-else>
              <p class="block mb-5 text-lg opacity-50">
                This model doesn't contain any records yet.
              </p>

              <div class="mb-2 flex items-center justify-center">
                <button class="rounded-btn border border-current lowercase text-primary text-sm mt-0 ml-3"
                  @click="populateRecords(5)">
                  Populate <span class="font-bold">5</span> records
                </button>

                <button class="rounded-btn border border-current lowercase text-primary text-sm mt-0 ml-3"
                  @click="populateRecords(10)">
                  Populate <span class="font-bold">10</span> records
                </button>

                <button class="rounded-btn border border-current lowercase text-primary text-sm mt-0 ml-3"
                  @click="populateRecords(25)">
                  Populate <span class="font-bold">25</span> records
                </button>
              </div>

              <div class="mx-auto mt-4" style="max-width: 500px">
                <DropArea 
                  placeholder="Drag and drop Pier table data JSON File here." 
                  @selected="handleTableDataSelected($event)"
                />
              </div>

              <!-- <button class="mb-2 rounded-btn border border-current lowercase text-primary text-sm mt-0 ml-1"
                @click="importJSON()">
                import JSON file
              </button> -->
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="py-5 flex items-center justify-between">
      <div class="text-sm flex items-center gap-2">
        Show

        <select class="border p-1 rounded" :value="modelFilters.perPage" @input="setFilter({'perPage': $event.target.value})">
          <option :value="5">5</option>
          <option :value="15">15</option>
          <option :value="25">25</option>
          <option :value="40">35</option>
        </select>

        items per page
      </div>
      
      <paginate v-if="records && records.length && !populatingRecords && !fetchingRecorsds && recordsPagination"
        :value="recordsPagination.current_page"
        :page-count="recordsPagination.last_page"
        :click-handler="handleFetchRecords"
        :prev-text="'Prev'"
        :next-text="'Next'"
        container-class="PierPagination flex items-center" 
      />
    </div>
  </div>
</template>

<script>
  import Paginate from 'vuejs-paginate';
  import TableRow from "./TableRow";
  import DropArea from "./DropArea";
  import { mapActions, mapState } from 'vuex';

  export default {
    name: 'PierCMSListTable',
    props: {
      model: {
        type: Object,
        default: function(){
          return {}
        }
      }
    },
    mounted(){
      this.handleFetchRecords();
    },
    computed: {
      ...mapState(['modelFilters', 'fetchingRecorsds', 'records', 'recordsPagination', 'populatingRecords']),
    },
    watch: {
      model: function(newValue){
        if(newValue)
          this.handleFetchRecords();
      },
    },
    methods: {
      ...mapActions(['setFilter', 'fetchRecords', 'populateRecords']),
      handleFetchRecords(page = 1){
        if(!this.model.fields)
          return;
        
        this.fetchRecords(page);
      },
      handleTableDataSelected(files) {
        if(files && files.length){
          const tableData = files[0];
          const reader = new FileReader();
          reader.onload = (e) => {
            const importedData = JSON.parse(e.target.result);
            for (let index = 0; index < importedData.length; index++) {
              const entry = importedData[index];
              console.log("Imported data entry: ", entry);
              this.$store.dispatch("createRecord", entry);
            }
          }
          reader.readAsText(tableData);
        }
      },
    },
    components: {
      TableRow,
      DropArea,
      Paginate
    }
  }
</script>