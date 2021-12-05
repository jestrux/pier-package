<template>
  <div class="relative overflow-hidden flex-shrink-0 h-56 bg-grey-500 -mt-5 -mx-5 mb-4">
    <img class="absolute pin object-cover w-full h-full" 
      :src="src"
      alt="" 
    />
  </div>
</template>

<script>

export default {
  name: "PierCardImage",
  props: {
    src: {
      type: String,
      default: "https://images.unsplash.com/photo-1516550893923-42d28e5677af?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ"
    },
  },
};
</script>