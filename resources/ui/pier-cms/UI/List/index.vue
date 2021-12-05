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

  #PierCMSList{
    padding: 20px 60px;
    width: 100%;
    min-height: 100%;
  }

  #PierCMSList.card{
    background: #efefef;
  }
</style>
<template>
  <Loader :size="90" v-if="!model || !model.fields" />
  <div v-else id="PierCMSList" :class="[model.settings.listPageType]">
    <PierCMSListCard v-if="model && model.settings.listPageType == 'card'" :model="model" />
    <PierCMSListTable v-else :model="model" />
  </div>
</template>

<script>
  import { fetchModelRecords } from "../../API";
  import { toPascalCase } from "../../Utils";
  import PierCMSListTable from "./PierCMSListTable";
  import PierCMSListCard from "./PierCMSListCard";

  import { mapState } from 'vuex';

  export default {
    name: 'PierCMSList',
    props: {
      model: {
        type: Object,
        default: function(){
          return {}
        }
      }
    },
    computed: {
      ...mapState(['records', 'fetchingRecords'])
    },
    components: {
      PierCMSListTable,
      PierCMSListCard
    }
  }
</script>