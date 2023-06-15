<template>
  <div class="h-screen flex-1 flex flex-col relative">
    <header id="mainNav">
      <span id="pageTitle" class="mr-2 font-bold">
        {{ modelName }}
      </span>

      <router-link
        :to="newEntryLink"
        class="border border-current flex font-semibold gap-1 items-center rounded-full text-primary text-sm leading-none hover:bg-neutral-200/50"
        style="padding: 0.4rem 1rem"
      >
        <svg
          class="-ml-1"
          height="18px"
          fill="currentColor"
          viewBox="0 0 24 24"
        >
          <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
        </svg>
        <span class="lowercase first-letter:uppercase inline-block"
          >New entry</span
        >
      </router-link>

      <span class="flex-1"></span>

      <div class="mr-8 flex gap-4 items-center">
        <model-filters v-if="selectedModel" :model="selectedModel" />

        <div class="relative rounded-full">
          <svg
            class="absolute inset-y-0 ml-3 my-auto inset-left-0 w-6 h-6"
            fill="#aaa"
          >
            <path
              d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"
            />
          </svg>

          <input
            class="rounded-full py-1 pl-10 pr-3 border border-gray-500 outline-none"
            style="min-width: 250px"
            type="search"
            :placeholder="`Search ${modelName}`"
            :value="modelFilters.q"
            @input="setFilter({ q: $event.target.value })"
          />
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
import { mapState, mapGetters, mapActions } from "vuex";
import ModelRecordList from "./List";
import ModelFilters from "./ModelFilters.vue";

export default {
  name: "PierCMS",
  props: {
    modelName: {
      type: String,
      required: true,
    },
  },
  mounted() {
    this.setupModel();
  },
  data() {
    return {
      model: {},
      popper: null,
      showFilters: false,
    };
  },
  computed: {
    ...mapState(["modelFilters"]),
    ...mapGetters(["selectedModel"]),
    newEntryLink() {
      const model = this.selectedModel;
      if(model && model.settings && model.settings.addOnNewPage)
        return `/${this.modelName}/add`;

      return `/${this.modelName}/list/add`;
    }
  },
  watch: {
    modelName: function () {
      this.setupModel();
    },
  },
  methods: {
    ...mapActions(["setFilter", "resetFilters", "setSelectedModel"]),
    setupModel() {
      this.resetFilters();

      if (!this.modelName) return;

      this.setSelectedModel(this.modelName);
    },
  },
  components: {
    ModelRecordList,
    ModelFilters,
  },
};
</script>