<style scoped>
#bcImageWrapper label {
  display: flex !important;
  align-items: center;
  justify-content: space-between;
  text-transform: capitalize;
}

#bcImageWrapper label:first-letter {
  text-transform: uppercase;
}

#bcImageWrapper input {
  background: transparent;
  -webkit-appearance: none;
  box-sizing: border-box;
  padding: 0.3em 0.5em;
  font-size: 1.25em;
  width: 100%;
  border: none;
  resize: none;
}

#bcImageWrapper button {
  display: inline-flex;
  align-items: center;
  border-radius: 30px;
  padding: 0.5em 1em;
  padding-right: 1.3em;
  border: none;
  cursor: pointer;
  text-transform: uppercase;
  margin-right: 0.2em;
  outline: none;
  letter-spacing: 0.15em;
  font-size: 0.65em;
}

#bcImageWrapper button:not(.active):hover {
  /* background: #e0e0e0; */
}

#bcImageWrapper button.active {
  cursor: default;
  background: #eaeaea;
}

#bcImageWrapper button svg {
  width: 18px;
  height: 18px;
  margin-right: 0.7em;
  margin-left: 0.2em;
}

#imageWrapper {
  position: relative;
  background: #f5f5f5;
  text-align: center;
}

#imageWrapper img {
  display: inline-block;
  max-width: 100%;
  max-width: 100%;
  min-height: 180px;
  max-height: 230px;
  object-fit: cover;
}
</style>

<template>
  <div id="bcImageWrapper">
    <label>
      <span class="inline-block first-letter:uppercase">
        {{ label ? label.replace(/_/g, " ") : '' }}
      </span>
      &nbsp;

      <template v-if="(src === null || !showPreview) && (imageUploadUrl || unsplashClientId)">
        <div v-if="showSearch || showUpload">
          <button
            type="button"
            v-if="showUpload"
            :class="{ active: source === 'upload' }"
            @click="source = 'upload'"
          >
            <svg viewBox="0 0 24 24">
              <path
                d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"
              />
            </svg>
            Upload
          </button>

          <button
            type="button"
            v-if="showSearch"
            :class="{ active: source === 'search' }"
            @click="source = 'search'"
          >
            <svg viewBox="0 0 24 24">
              <path fill="none" d="M0 0h24v24H0V0z" />
              <path
                d="M18 13v7H4V6h5.02c.05-.71.22-1.38.48-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-5l-2-2zm-1.5 5h-11l2.75-3.53 1.96 2.36 2.75-3.54zm2.8-9.11c.44-.7.7-1.51.7-2.39C20 4.01 17.99 2 15.5 2S11 4.01 11 6.5s2.01 4.5 4.49 4.5c.88 0 1.7-.26 2.39-.7L21 13.42 22.42 12 19.3 8.89zM15.5 9C14.12 9 13 7.88 13 6.5S14.12 4 15.5 4 18 5.12 18 6.5 16.88 9 15.5 9z"
              />
            </svg>
            Search
          </button>

          <button
            type="button"
            :class="{ active: source === 'link' }"
            @click="source = 'link'"
          >
            <svg viewBox="0 0 24 24">
              <path
                d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"
              />
            </svg>
            Link
          </button>
        </div>
      </template>

      <button
        class="text-primary font-semibold"
        type="button"
        v-else-if="src !== null"
        @click="src = null"
      >
        Change
      </button>
    </label>

    <div v-if="src === null || !showPreview">
      <LinkField
        v-if="source === 'link'"
        v-model="src"
        placeholder="Click paste to paste image link"
        match-error="Invalid image link"
      />

      <unsplash-search
        :client-id="unsplashClientId"
        per-page="6"
        v-else-if="showSearch && source === 'search'"
        v-model="src"
      />

      <file-uploader
        v-else-if="showUpload && source === 'upload'"
        v-model="src"
        :upload-url="imageUploadUrl"
        type="image"
      />
    </div>

    <FieldPreview v-else-if="src !== null" type="image" :url="src" :isDp="isDp" />

    <div style="position: relative; pointer-events: none">
      <input
        style="position: absolute; top: -3rem; opacity: 0"
        type="text"
        :value="src"
        :required="required"
      />
    </div>
  </div>
</template>

<script>
import FileUploader from "../FileUploader";
import UnsplashSearch from "./UnsplashSearch";
import LinkField from "../LinkField";
import FieldPreview from "../FieldPreview";

export default {
  props: {
    url: String,
    label: {
      type: String,
      default: "Image",
    },
    meta: Object,
    required: Boolean,
  },
  inject: ["PierCMSConfig"],
  mounted() {
    if(this.showUpload) this.source = 'upload';

    this.$nextTick(() => {
      this.src = this.url && this.url.length ? this.url : null;
    });
  },
  data() {
    return {
      enteringLink: false,
      src: null,
      source: 'link',
    };
  },
  computed: {
    isDp() {
      return this.meta && this.meta.face;
    },
    showPreview() {
      if(!this.meta || this.meta.showPreview == undefined) return true;

      return this.meta.showPreview;
    },
    unsplashClientId() {
      return this.PierCMSConfig.unsplashClientId;
    },
    showSearch() {
      const metaShow = !this.meta || this.meta.showSearch == undefined || this.meta.showSearch;
      return metaShow && this.unsplashClientId && this.unsplashClientId.length;
    },
    showUpload() {
      const metaShow = !this.meta || this.meta.showUpload == undefined || this.meta.showUpload;
      return metaShow && this.imageUploadUrl && this.imageUploadUrl.length;
    },
    imageUploadUrl() {
      const s3ConfigSet =
        this.PierCMSConfig.s3 &&
        Object.values(this.PierCMSConfig.s3).filter((v) => v && v.length)
          .length == 4;
      if (s3ConfigSet) return "s3";

      return this.PierCMSConfig.fileUploadUrl;
    },
  },
  watch: {
    src: {
      immediate: true,
      handler(val) {
        if (val != null) this.$emit("input", val);
      },
    },
  },
  components: {
    FileUploader,
    UnsplashSearch,
    LinkField,
    FieldPreview,
  },
};
</script>