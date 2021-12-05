<style>
    #linkInputWithPreview{
        position: relative;
        border-radius: 2px;
        box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.3);
    }

    #linkInputWithPreview.has-preview{
        padding-bottom: 9px;
    }

    #linkInputWithPreview input{
        box-shadow: none;
    }
    
    #linkInputWithPreview #FieldPreview{
        box-shadow: none;
        border: none;
        background: #ebf0f5;
        border-radius: 7px;
        margin: 0 0.5rem;
        padding: .4rem .6rem;
    }
    
    #linkInputWrapper{
        position: relative;
    }

    #linkInputWrapper.has-error{
        margin-bottom: 2rem;
    }
    
    #linkInputWrapper input{
        /* pointer-events: none; */
    }

    #linkInputWrapper:after{
        content: '';
        position: absolute;
        right: 0.4rem;
        bottom: 5px;
        top: 5px;
        width: 65px;
        background: #fff;
        background: linear-gradient(90deg, rgba(255,255,255,0.3) 8%, rgba(255,255,255,1) 22%);
    }
    
    #linkInputWrapper button{
        text-transform: uppercase;
        letter-spacing: 0.15em;
        border: 1px solid #999;
        border-radius: 3px;
        padding: 0.15rem 0.4rem;
        padding-bottom: 0.2rem;
        background: transparent;
        outline: none;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0;
        margin: 0 0.4rem;
        font-size: 0.6rem;
        z-index: 1;
    }

    #linkInputWrapper.has-error:before{
        position: absolute;
        content: attr(error);
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        color: #fc8181;
        display: block;
        bottom: -1.5rem;
        left: 0;
        font-size: 0.85rem;
        letter-spacing: 0.045em;
    }
</style>
<template>
    <div id="linkInputWithPreview" :class="{'has-preview': canShowPreview && previewReady}">
        <div id="linkInputWrapper" :error="error" :class="{'has-error': error}">
            <input type="text" 
                ref="input"
                v-model="val"
                :placeholder="placeholderText" 
            />

            <button type="button" @click="pasteFromClipboard">
                {{ val && val.length ? 'Clear' : 'Paste'}}
            </button>
        </div>

        <FieldPreview v-if="canShowPreview"
            type="link"
            @preview-ready="previewReady = true" 
            :url="val"
        />
    </div>
</template>

<script>
const linkMatchPattern = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
const youtubeMatchPattern = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;

import FieldPreview from "./FieldPreview";

export default {
    name: "LinkField",
    props: {
        value: String,
        label: String,
        placeholder: String,
        showPreview: Boolean,
        matchPattern: {
            type: RegExp,
            default: function(){
                return linkMatchPattern;
            }
        },
        matchError: {
            type: String,
            default: "Invalid URL"
        }
    },
    mounted() {
        if(this.value){
            if(this.value == "false"){
                // if(this.showPreview)
                //     this.val = null;
                    
                this.val = "";
            }
        }
    },
    data() {
        return {
            val: false,
            dontUpdate: false,
            previewReady: false,
            error: null
        }
    },
    computed: {
        canShowPreview(){
            return this.showPreview
                && (!this.error || !this.error.length) 
                && this.val && this.val.length;
        },
        placeholderText(){
            if(this.placeholder)
                return this.placeholder;

            const label = this.label || "link";
            return `Click paste to paste ${label.toLowerCase()}`;
        }
    },
    methods: {
        _isValidURL : function(str){
            if(this.matchPattern == "youtube"){
                const match = this._parseYoutubeUrl(str);
                return match && match[2] && match[2].length >= 11;
            }

            return this.matchPattern.test(str);
        },
        async getClipboardText(){
            try {
                const text = await navigator.clipboard.readText();
                return text;
            } catch (error) {
                console.error('Failed to read clipboard contents: ', err);
                return false;
            }
        },
        async pasteFromClipboard() {
            this.error = null;
            if(this.val && this.val.length){
                this.val = "";
                return;
            }

            try {
                const text = await this.getClipboardText();
                if(this._isValidURL(text))
                    this.val = text;
                else{
                    this.error = this.matchError;

                    setTimeout(() => {
                        this.error = null;
                    }, 1200);
                }
            } catch (error) {
                this.error = "Unknown error occured";
                console.log("Failed to paste link: ", error);
            }
        },
        _parseYoutubeUrl(url){
            var reg = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/
            return url.match(reg);
        }
    },
    watch: {
        value: function(newValue){
            this.dontUpdate = true;
            if(newValue == "false")
                newValue = "";

            this.val = newValue;
            this.$emit('input', newValue);
        },
        val: function(newValue){
            this.previewReady = false;
            this.$emit('input', newValue);
        }
    },
    components: {
      FieldPreview
    }
}
</script>