<template>
    <div class="flex items-center">
        <div class="hidde relative flex-shrink-0 bg-grey-500 rounded-full"
          :class="{
            'w-12 h-12 mr-3': !hidePosition,
            'w-8 h-8 mr-2': hidePosition
          }"
        >
          <img class="absolute pin rounded-full object-cover w-full h-full" 
            :src="image"
            alt="" 
          />
        </div>

        <div>
          <h5 class="text-lg" :class="{'font-bold': !hidePosition}">
            {{ name }}
          </h5>
          <p v-if="!hidePosition" class="capitalize text-gray-800">{{ position }}</p>
        </div>
    </div>
</template>

<script>

export default {
  name: "PierCardMiniProfile",
  props: {
    image: {
      type: String,
      default: "https://images.unsplash.com/photo-1551069613-1904dbdcda11?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ"
    },
    name: {
      type: String,
      default: "Agnes Mng'one"
    },
    position: {
      type: String
    }
  },
  computed: {
    hidePosition(){
      return !this.position || !this.position.length;
    }
  }
};
</script>