<template>
    <div>
        <img :class="[options.type]" :src="src" :alt="options.caption">
        <small v-text="options.caption"></small>
    </div>
</template>

<script>
import BcElement from './BcElement.vue';
export default {
    extends: BcElement,
    props: {
        location : String
    },
    computed: {
        src: function(){
            return this.location + "/" + this.options.source
        }
    }
}
</script>