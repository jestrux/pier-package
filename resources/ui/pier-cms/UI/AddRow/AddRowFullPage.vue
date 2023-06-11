<template>
  <div class="h-screen flex-1 flex flex-col relative">
    <header id="mainNav">
      <router-link
        :to="`/${selectedModelName}/list`"
        class="inline-flex gap-2 items-center rounded border-none hover:bg-black/5 text-neutral-600 py-1 pl-1 pr-3"
      >
        <svg
          class="h-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.75 19.5L8.25 12l7.5-7.5"
          />
        </svg>

        <span>Back</span>
      </router-link>

      <span class="mr-24 flex-1 text-center text-xl leading-none font-bold">
        {{ (values && values._id ? "Edit" : "Add") + " " + selectedModelName }}
      </span>
    </header>

    <div id="mainContent" class="py-2">
      <div
        class="bg-white rounded-xl shadow my-4 pt-2 pb-5 px-6 mx-auto w-full"
        style="max-width: 680px"
      >
        <AddRowForm
          :values="values"
          :model="selectedModel"
          :saving="savingRecord"
          @save="onSave($event)"
          :on-full-page="true"
        />
      </div>

      <template v-if="referenceModel">
        <Modal :title="'Add ' + referenceModel.name">
          <AddRowForm
            :model="referenceModel"
            :saving="savingReferenceModel"
            @save="onSave($event, true)"
            @close="closeReferenceModal"
          />
        </Modal>
      </template>
    </div>
  </div>
</template>

<script>
import AddRowForm from "./AddRowForm";
import Modal from "../../../components/Modal";
import AddRowMixin from "./AddRowMixin";

export default {
  name: "PierCMSAddRowFullPage",
  mixins: [AddRowMixin],
  components: {
    AddRowForm,
    Modal,
  },
};
</script>