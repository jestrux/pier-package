<template>
    <div id="container">
        <aside class="flex-shrink-0">
            <div class="long-header bg-blue-100 text-blue-800 tracking-wider">
                <!-- <img class="mb-2" style="height:50px; margin-left: -8px" src="img/logo.png" alt=""> -->
                Pier CMS
            </div>
            <ul>
                <li v-for="model in models"
                    :key="model._id">
                    <router-link :to="`/${model.name}`"
                        :class="{'active': selectedModelName.replace('%20', ' ') === model.name}">
                        {{ model.name }}
                    </router-link>
                </li>
            </ul>
        </aside>

        <main class="flex-1 h-screen">
            <router-view />
        </main>
    </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
    name: 'PierCMSWrapper',
    mounted(){
        if(this.$route.path === "/"){
            const modelName = window.models[0].name;
            this.$store.dispatch('setSelectedModel', modelName);
            this.$router.replace("/" + modelName);
        }
        else if(!this.selectedModelName || !this.selectedModelName.length){
            this.$store.dispatch('setSelectedModel', this.$route.path.split("/")[1]);
        }

        if(!this.models){
            this.$store.dispatch('setModels', window.models);
        }
    },
    computed: {
        ...mapState(['models', 'selectedModelName'])
    }
}
</script>