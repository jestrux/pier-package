<template>
    <div style="background: #f5f5f5" class="border- mt-5 -mb-5 -mx-5 px-5 py-4 flex items-center justify-center">
        <button class="inline-flex items-center justify-center text-base text-sm bg-orange-100 bg-opacity-50 border border-orange-500 text-orange-700 py-1 pl-4 pr-6 rounded-full mr-4" type="button">
            <svg fill="currentColor" class="mr-3 w-4 h-4" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            EDIT
        </button>
        
        <button class="inline-flex items-center justify-center text-base text-sm border border-red-400 bg-red-100 bg-opacity-50 text-red-500 py-1 pl-4 pr-6 rounded-full" type="button">
            <svg fill="currentColor" class="mr-2 w-4 h-4" viewBox="0 0 24 24">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9zm7.5-5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
            DELETE
        </button>
    </div>
</template>

<script>

export default {
  name: "CardActions",
  props: {
    data: Object
  },
  data() {
    return {
      
    };
  },
};
</script>