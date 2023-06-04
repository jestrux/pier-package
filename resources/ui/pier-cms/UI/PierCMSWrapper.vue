<template>
    <div id="container">
        <aside class="flex-shrink-0">
            <div class="long-header">
                <div v-if="PierCMSConfig.appLogo" class="w-16 h-16 p-2 bg-white rounded-full overflow-hidden flex items-center justify-center">
                    <img class="w-full max-h-full" :src="PierCMSConfig.appLogo" alt="" />
                </div>
                {{PierCMSConfig.appName ? PierCMSConfig.appName + " " : ""}}CMS
            </div>
            <ul>
                <li v-for="model in models"
                    :key="model._id">
                    <router-link :to="`/${model.name}`"
                        :class="{'active': selectedModelName.replace('%20', ' ') === model.name}">
                        {{ model.plural_name }}
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
import * as API from "../../API";

export default {
    name: 'PierCMSWrapper',
    inject: ['PierCMSConfig'],
    provide() {
        return {
            API
        };
    },
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