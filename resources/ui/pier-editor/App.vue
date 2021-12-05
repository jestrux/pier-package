<template>
  <CThemeProvider>
    <CReset />

    <router-view />
  </CThemeProvider>
</template>

<script>
  import { CThemeProvider, CReset } from '@chakra-ui/vue'

  export default {
    name: 'App',
    mounted(){
    //   this.$store.dispatch('setup');
    },
    computed: {
    //   ...mapState(['initialized'])
    },
    components: {
      CThemeProvider,
      CReset
    }
  }
</script>