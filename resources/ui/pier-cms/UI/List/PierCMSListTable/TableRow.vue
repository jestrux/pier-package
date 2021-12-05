<style>
  .pier-tr{
    /* max-width: 120px; */
    /* overflow: hidden; */
  }
</style>
<template>
  <tr class="text-capitalize" v-if="fields && data">
    <template v-for="(field, index) in fields">
      <TableColumn 
        v-if="field.type !== 'password'"
        :key="index"
        :field="field"
        :value="data[field.label]"
      />
    </template>
    <td class="text-center">
      <span class="inline-flex items-center justify-center">
        <router-link :to="`${$route.path.replace('list/', 'detail').replace('list', 'detail')}/${data._id}`" class="border-none bg-transparent border-orange-500 text-gray-600 mr-2 py-1 px-2">
          <svg fill="currentColor" height="20" width="20" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
        </router-link>
        <router-link :to="`${$route.path.replace('list/', 'list').replace('list', 'list')}/${data._id}/edit`" class="border-none bg-transparent text-orange-400 mr-2 py-1 px-2">
          <svg fill="currentColor" height="18" width="18" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        </router-link>
        <router-link :to="`${$route.path.replace('list/', 'list').replace('list', 'list')}/${data._id}/delete`" class="border-none bg-transparent text-red-500 py-1 px-2">
          <svg fill="currentColor" height="20" width="20" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9zm7.5-5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
        </router-link>
      </span>
    </td>
  </tr>
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