<style scoped>
  .pier-model-link{
    box-shadow: none;
  }
</style>
<template>
  <c-dark-mode>
    <div id="PierApp" class="h-screen bg-dark-200 flex text-white">
      <aside class="h-full bg-dark-300" style="width: 23%; min-width: 300px">
        <div class="border-b border-gray-600 border-opacity-25 px-6 py-3">
          <div class="flex items-center justify-between mb-3">
            <c-text fontSize="3xl">
              Pier
            </c-text>

            <c-button v-if="models && models.length" size="sm" variant="outline" variant-color="gray"
              @click="exportDb()"
            >
              <c-box mr="2" mb="0.5">
                <c-icon name="triangle-down" size="9px" />
              </c-box>
              Export Db
            </c-button>
          </div>

          <c-button size="lg" width="100%" variant-color="orange" mb="2"
            :disabled="$route.path === '/models/add'"
            @click="$router.push('/models/add')">
            <c-box d="inline-flex" align-items="center">
              <c-box mr="4" mb="1">
                <c-icon name="add" size="14px" />
              </c-box>
              New Pier Model
            </c-box>
          </c-button>
        </div>

        <c-box py="3">
          <c-box v-if="fetchingModels" d="flex" padding="6" align-items="center" justify-content="center">
            <c-circular-progress color="orange" is-indeterminate />
          </c-box>
          
          <c-box v-else-if="!models" d="flex" padding="6" align-items="center" justify-content="center">
            <c-text fontSize="xl">
              Failed to fetch models.
            </c-text>
          </c-box>
          
          <c-box v-else>
            <c-button
              class="pier-model-link"
              size="lg" px="6" py="4" v-for="(model, index) in models" 
              :key="index"
              width="100%"
              :variant-color="modelBeingEdited && model._id === modelBeingEdited._id ? 'orange.300' : 'gray'"
              backgroundColor="transparent"
              :color="modelBeingEdited && model._id === modelBeingEdited._id ? 'orange.200' : '#999'"
              borderRadius="0"
              paddingLeft="1.8rem"
              :disabled="$route.path === '/models/add'"
              @click="$router.push(`/models/${model._id}/details`)"
            >
              <div class="text-left" style="width: 100%;">
                {{ model.name }}
              </div>
            </c-button>
          </c-box>
        </c-box>
      </aside>
      
      <main class="h-full flex-1 flex flex-col">
        <div class="flex-1 bg-dark-400 overflow-auto">
          <router-view />
        </div>
      </main>
    </div>
  </c-dark-mode>
</template>

<script>

import { 
  CText, CLink, CButton, CIcon, CDarkMode, CBox,
  CCircularProgress, CCircularProgressLabel
} from '@chakra-ui/vue';
import { mapState, mapGetters } from 'vuex';
import { BASE_URL } from '../services/API/setup';

export default {
  name: "DefaultContainer",
  mounted() {
    if(!this.models)
      this.$store.dispatch('getModels');
  },
  data() {
    return {};
  },
  computed: {
    ...mapState(['models', 'fetchingModels']),
    ...mapGetters(['modelBeingEdited']),
    name() {
      return this.$route.name;
    }
  },
  methods: {
    async logout() {},
    exportDb(){
      fetch(BASE_URL + "/admin/pier-export-data")
        .then((response) => response.blob())
        .then((blob) => {
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "pier-db.json";
            link.click();
        })
        .catch(console.error);
    }
  },
  components: {
    CText,
    CLink,
    CButton, CIcon, CDarkMode, CBox,
    CCircularProgress, CCircularProgressLabel
  }
};
</script>