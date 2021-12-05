<style scoped>
#fileUploader {
  position: relative;
}
img {
  width: 100%;
  max-height: 300px;
  object-fit: cover;
  object-position: center;
}
#fileDrop {
  cursor: pointer;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 120px;
  background-color: #ddd;
  border-bottom: 1px solid #999;
  padding: 1em;
  text-align: center;
  font-size: 0.9em;
  border: 3px dashed #ccc;
}
#fileDrop.hover {
  border-color: #888;
  margin: 2px;
}
#loader {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.97);
  color: #854dfe;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-family: "Courier New", Courier, monospace;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: opacity 0.35s ease-out;
}
#loader span {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 4px dashed #1ddc6a;
  border-left-width: 2px;
  border-bottom-width: 2px;
  margin-bottom: 2em;
  animation: roll 0.7s ease-out infinite alternate;
}
#loader.hide {
  opacity: 0;
  pointer-events: none;
}
#fileDrop:before {
  content: attr(placeholder);
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  margin: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}
#error {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  text-align: center;
  background: rgba(199, 76, 76, 0.97);
  color: #fff;
}
#error svg {
  height: 36px;
  width: 36px;
  fill: #672827;
}
#error p {
  margin-top: 0.4em;
  margin-bottom: 1em;
  font-family: verdana, sans-serif;
  letter-spacing: 0.02em;
}

#error #errorCloser {
  line-height: 0;
  letter-spacing: 0.1em;
  display: block;
  font-size: 0.85em;
  text-transform: uppercase;
  padding: 1.1em 1em;
  background-color: #d06e6c;
  color: #ffeaea;
  font-family: verdana, sans-serif;
  cursor: pointer;
}
</style>

<template>
  <div id="fileUploader">
    <label id="fileDrop" for="fileDropInput" :placeholder="placeholder"></label>
    <input id="fileDropInput" type="file" class="hidden" @change="handleFileSelected($event)" />
  </div>
</template>

<script>
import FileDrag from "./FileDrag";

export default {
  name: "DropArea",
  props: {
    "placeholder": {
      default: "Drop your file here to upload."
    }
  },
  mounted: function () {
    const self = this;
    const { em } = new FileDrag(this.$el);
    em.on("selected", self.handleFileSelected);
  },
  methods: {
    handleFileSelected(e){
      var files = e.target.files || e.dataTransfer.files;

      if(!files || !files.length) return;

      this.$emit("selected", files);
    }
  }
};
</script>