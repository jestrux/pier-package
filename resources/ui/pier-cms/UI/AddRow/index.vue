<template>
  <div>
    <div :style="{ display: referenceModel ? 'none' : '' }">
      <Modal
        :title="
          (values && values._id ? 'Edit' : 'Add') + ' ' + selectedModelName
        "
      >
        <AddRowForm
          :values="values"
          :model="selectedModel"
          :saving="savingRecord"
          @save="onSave($event)"
          @close="$router.replace(`/${selectedModelName}`)"
        />
      </Modal>
    </div>

    <template v-if="referenceModel">
      <Modal
        :title="'Add ' + referenceModel.name"
        form-id="addRowFormPopup"
        :show-close-button="true"
        @close="closeReferenceModal"
        show-save-button="true"
        :saving="savingReferenceModel"
      >
        <AddRowForm
          form-id="addRowFormPopup"
          :model="referenceModel"
          :hide-action-buttons="true"
          @save="onSave($event, true)"
        />
      </Modal>
    </template>
  </div>
</template>

<script>
import AddRowForm from "./AddRowForm";
import Modal from "../../../components/Modal";
import AddRowMixin from './AddRowMixin';

export default {
  name: "PierCMSAddRow",
  mixins: [AddRowMixin],
  components: {
    AddRowForm,
    Modal,
  },
};
</script>