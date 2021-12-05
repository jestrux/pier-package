<style>
  .TaskCard h3.text-2xl{
    line-height: 1.4;
    font-size: 1.6rem;
  }
</style>
<template>
  <div class="PierCard TaskCard rounded-lg overflow-hidden bg-white text-black p-5">
    <div v-if="!noStatus || !noDate" class="flex justify-between items-center">
      <div v-if="!noStatus" class="mb-3 flex items-center">
        <span class="rounded-full px-3 py-1 bg-green-200 text-green-900 white font-bold uppercase text-sm tracking-wider">
          {{ status }}
        </span>
      </div>

      <PierCardDate v-if="!noDate" icon="event" :date="date" />
    </div>

    <PierCardHeading v-if="!noTitle"
      :heading="title"
      headingSize="2xl"
    />

    <div class="flex justify-between mt-4"
      v-if="(!noAssigneeImage && !noAssigneeName) || !noReviewers"
    >
      <div v-if="!noAssigneeImage && !noAssigneeName">
        <span class="tracking-widest uppercase text-sm text-gray-700 inline-block mb-2">
          {{ assigneeLabel }}
        </span>

        <PierCardMiniProfile v-if="!noAssigneeImage && !noAssigneeName"
          :name="assigneeName"
          :image="assigneeImage"
        />
      </div>

      <div v-if="!noReviewers">
        <span class="tracking-widest uppercase text-sm text-gray-700 inline-block mb-2">
          {{ reviewersLabel }}
        </span>

        <div class="flex items-center">
          <div class="flex items-center">
            <div class="relative flex-shrink-0 w-10 h-10 border-4 border-white bg-grey-500 rounded-full -mr-3"
              v-for="(reviewer, index) in reviewers.slice(0,3)" :key="index"
            >
            <img class="absolute pin rounded-full object-cover w-full h-full"
              :src="reviewer"
              alt
            />
            </div>
          </div>

          <span v-if="reviewers.length > 3" class="text-lg text-gray-700 ml-3">
            +{{ reviewers.length - 3 }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  PierCardHeading,
  PierCardDate,
  PierCardMiniProfile
} from "../CardComponents";

export default {
  name: "TaskCard",
  props: {
    values: Object,
  },
  mounted(){
    this.bindValues();
  },
  data() {
    return {
      status: "Complete",
      date: "2020-09-23",
      title: "Recruit and onboard 20 new interns for the summer program",
      assigneeLabel: "Assigned to",
      assigneeImage: "https://images.unsplash.com/photo-1586367751368-99141fd186a0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      assigneeName: "Frank Abel",
      reviewersLabel: "Reviewers Label",
      reviewers: [
        "https://images.unsplash.com/photo-1542513217-0b0eedf7005d?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
        "https://images.unsplash.com/photo-1546672741-d327539d5f13?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
        "https://images.unsplash.com/photo-1587492520470-8cea42e7b7fe?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      ],
    };
  },
  computed: {
    noStatus(){
      return !this.status || !this.status.length;
    },
    noDate(){
      return !this.date || !this.date.length;
    },
    noTitle(){
      return !this.title || !this.title.length;
    },
    noAssigneeImage(){
      return !this.assigneeImage || !this.assigneeImage.length;
    },
    noAssigneeName(){
      return !this.assigneeName || !this.assigneeName.length;
    },
    noReviewers(){
      return !this.reviewers || !this.reviewers.length;
    },
    noCard(){
      return this.noStatus && !this.noDate && !this.noTitle 
        && !this.noAssigneeImage && !this.noAssigneeName
        && !this.noReviewers;
    }
  },
  watch:{
    values:{
      deep: true,
      handler: function(){
        this.bindValues();
      }
    }
  },
  methods: {
    bindValues(){
      if(!this.values) return;

      const {
        status,
        date,
        title,
        assigneeLabel,
        assigneeImage,
        assigneeName,
        reviewersLabel,
        reviewers
      } = this.values;

      this.status = status;
      this.date = date;
      this.title = title;
      this.assigneeLabel = assigneeLabel;
      this.assigneeImage = assigneeImage;
      this.assigneeName = assigneeName;
      this.reviewersLabel = reviewersLabel;
      this.reviewers = reviewers;
    }
  },
  components: {
    PierCardHeading,
    PierCardDate,
    PierCardMiniProfile
  },
};
</script>