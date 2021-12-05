<style scoped>
  .pier-th{
    text-transform: capitalize;
    text-align: left;
    padding: 0;
    padding-top: 1rem;
  }
</style>
<template>
  <tr v-if="!fields || !data" />
  <tbody v-else>
    <template v-for="(field, index) in fields">
      <template v-if="field.type !== 'password'">
        <tr :key="'tr' + index">
          <th class="text-capitalize text-xl" :class="['pier-th', field.type, field.meta ? field.meta.type : '']"
          >
            {{ field.label.replace(/_/g, ' ') }}
          </th>
        </tr>
        <tr :key="'' + index">
          <TableColumn 
            :key="index"
            :field="field"
            :value="data[field.label]"
          />
        </tr>
      </template>
    </template>
  </tbody>
</template>

<script>
  import TableColumn from "./TableColumn";
  export default {
    name: 'PierCMSTableRow',
    props: {
      data: {
        type: Object,
        default: function(){
          return {}
        }
      },
      fields: {
        type: Array,
        default: function(){
          return []
        }
      }
    },
    components: {
      TableColumn
    }
  }
</script>