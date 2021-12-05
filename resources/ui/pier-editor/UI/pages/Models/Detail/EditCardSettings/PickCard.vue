<style>
    .PierCardOption{
      position: relative;
      text-align: left;
      background: transparent;
      /* background: #fff; */
      outline: none !important;
      box-shadow: none !important;
      border: 4px solid transparent;
      border-radius: 10px;
      overflow: hidden;
      cursor: pointer;
    }

    .PierCardOption:not(.selected):hover{
      opacity: 0.85;
    }

    .PierCardOption.selected{
      border-color: #ffba7f;
      pointer-events: none;
    }

    .PierCardOption:before{
      background: #ffba7f;
      width: 160px;
      height: 80px;
      position: absolute;
      right: -70px;
      top: -30px;
      transform: rotate(45deg);
      z-index: 40;
      border-radius: 0 0 90px 90px;
    }
    
    .PierCardOption:after{
      color: #fff;
      position: absolute;
      right: 5px;
      top: 5px;
      z-index: 42;
      width: 30px;
      height: 30px;
      background: url("data:image/svg+xml;utf8, <svg xmlns='http://www.w3.org/2000/svg' width='26' height='26' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'><polyline points='20 6 9 17 4 12'></polyline></svg>") no-repeat;
    }

    .PierCardOption.selected:before,
    .PierCardOption.selected:after{
      content: "";
    }

    .PierCardOption > .PierCard{
      margin: -4px 0;
      border-radius: 0 !important;
    }
    
    .PierCardOption *{
      pointer-events: none !important;
    }
</style>
<template>
    <c-box d="grid" grid-template-columns="1fr 1fr" grid-gap="5" class="flex-wrap items-start">
      <button type="button" class="PierCardOption"
        :class="{'selected': value === 'TaskCard'}"
        @click="setSelectedCard('TaskCard')"
      >
        <TaskCard />
      </button>
      
      <button type="button" class="PierCardOption"
        :class="{'selected': value === 'ProfileCard'}"
        @click="setSelectedCard('ProfileCard')"
      >
        <ProfileCard />
      </button>
    </c-box>
</template>

<script>

import { CBox } from '@chakra-ui/vue';
import ArticleCard from "../CardOptions/ArticleCard";
import EventCard from "../CardOptions/EventCard";
import ProfileCard from "../CardOptions/ProfileCard";
import TaskCard from "../CardOptions/TaskCard";
import ShopCard from "../CardOptions/ShopCard";
import CourseCard from "../CardOptions/CourseCard";

export default {
  name: "PickCard",
  props: {
    value: String
  },
  methods: {
    setSelectedCard(card){
      this.$emit('input', card);
    }
  },
  components: {
    CBox,
    ArticleCard,
    EventCard,
    ProfileCard,
    TaskCard,
    ShopCard,
    CourseCard,
  }
};
</script>