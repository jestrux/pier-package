<template>
  <div class="modal open">
    <div
      class="modal-content rounded-t-md"
      style="
        width: 570px;
        align-self: flex-start;
        margin-top: 3rem;
        border-radius: 12px;
      "
    >
      <div class="modal-title flex items-center">
        <button
          v-if="showCloseButton"
          type="button"
          class="flex-shrink-0 mr-2.5 bg-black/10 hover:bg-black/20 rounded flex items-center justify-center w-7 h-7"
          @click="$emit('close')"
        >
          <svg
            width="13"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="3"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"
            />
          </svg>
        </button>

        <h3 class="title">
          {{ title }}
        </h3>

        <button
          v-if="showSaveButton"
          :form="formId"
          class="ml-auto text-sm bg-primary text-white py-2 px-4 rounded"
          :class="{
            'pointer-events-none opacity-50': saving,
          }"
          type="submit"
        >
          {{ saving ? `SAVING...` : "SAVE" }}
        </button>
      </div>

      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "Modal",
  props: ["title", "showCloseButton", "showSaveButton", "saving", "formId"],
};
</script>