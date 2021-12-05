<template>
  <div class="mb-3 flex items-center">
    <span class="text-gray-500">
        <svg fill="currentColor" class="mr-3 w-5" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
        </svg>
    </span>
    <span class="text-lg text-gray-700">
        {{ location }}
    </span>
  </div>
</template>

<script>

export default {
  name: "PierCardLocation",
  props: {
    location: {
      type: String,
      default: "Ngarenaro, Arusha Tanzania"
    }
  }
};
</script>