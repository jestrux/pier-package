<style scoped>
    #fileUploader{
      position: relative;
    }

    img{
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      object-position: center;
    }

    #fileDrop{
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 120px;
      background-color: #e8e8e8;
      border-bottom: 1px solid #eee;
      padding: 1em;
      text-align: center;
      font-size: 0.9em;
      border: 3px dashed transparent;
    }

    #fileDrop.hover{
      border-color: #ccc;
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
      font-family: 'Courier New', Courier, monospace;
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
      pointer-events: none
    }

    #fileDrop:before{
      content: 'Drop your file here to upload.';
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

    #error{
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

    #error svg{
      height: 36px;
      width: 36px;
      fill: #672827;
    }

    #error p{
      margin-top: 0.4em;
      margin-bottom: 1em;
      font-family: verdana, sans-serif;
      letter-spacing: 0.02em;
    }
    
    #error #errorCloser{
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
    <img :src="src" alt="" width="100%" v-if="src">

    <div id="fileDrop" v-else></div>

    <div id="loader" v-if="uploading">
      <span></span>
      Uploading... {{progress}}%
    </div>
    
    <div id="error" v-if="upload_error != null">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
      <p>{{ upload_error }}</p>
      <span id="errorCloser" @click="upload_error = null">Close</span>
    </div>
  </div>
</template>

<script>
  import FileDrag from "./filedrag";

  // TODO: Fix second upload error

  var self;
  
  export default {
    props: {
      uploadUrl: String,
      location: String,
    },
    data: function() {
      return{
        uploading: false,
        upload_error: null,
        progress: 0,
        src: ""
      }
    },
    mounted: function() {
      this.setup();
    },
    methods: {
      setup(){
        const self = this;

        const { em } = new FileDrag(this.$el, this.uploadUrl);

        em.once('loaded', function(file, src) {
          self.src = src;
          self.uploading = true;
        });
        
        em.once('progressed', function(progress) {
          // console.log("Progress changed: ", progress);
          self.progress = progress;
        });
        
        em.once('complete', function(status, payload) {
          self.uploading = false;
          console.log(status, payload);
          // console.log("Completed successfully: ", status, payload);

          if(status)
            self.$emit("input", payload);
          else{
            self.upload_error = payload;
            self.src = null;
          }
        });
      }
    }
  }
</script>