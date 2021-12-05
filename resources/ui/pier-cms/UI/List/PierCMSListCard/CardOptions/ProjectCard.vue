<style>
    .ProjectCard h3.text-2xl{
        line-height: 1.4;
        font-size: 1.6rem;
    }
</style>
<template>
  <div class="PierCard ProjectCard rounded-lg overflow-hidden bg-white text-black p-5">
    <div class="flex justify-between items-center">
        <div class="mb-3 flex items-center">
            <span class="hidde rounded-full px-3 py-1 bg-green-200 text-green-900 white font-bold uppercase text-sm tracking-wider">
            Complete
            </span>
        </div>

        <PierCardDate date="2020-09-23" />
    </div>

    <PierCardHeading
        :heading="title"
        headingSize="2xl"
    />

    <div class="flex justify-between mt-4">
        <div>
            <span class="tracking-widest uppercase text-sm text-gray-700 inline-block mb-2">
                Assigned to
            </span>

            <PierCardMiniProfile 
                :name="assigneeName"
                :image="assigneeImage"
                :position="assigneePosition"
            />
        </div>

        <div>
            <span class="tracking-widest uppercase text-sm text-gray-700 inline-block mb-2">
                Reviewers
            </span>

            <div class="flex items-center">
                <div class="flex items-center">
                    <div class="relative flex-shrink-0 w-10 h-10 border-4 border-white bg-grey-500 rounded-full -mr-3"
                        v-for="(reviewer, index ) in reviewers" :key="index"
                    >
                    <img class="absolute pin rounded-full object-cover w-full h-full"
                        :src="reviewer"
                        alt
                    />
                    </div>
                </div>

                <span class="text-lg text-gray-700 ml-3">+3</span>
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
  name: "ProjectCard",
  props: {
    data: Object,
  },
  data() {
    return {
      title: "Recruit and onboard 20 new interns for the summer program",
      assigneeImage: "https://images.unsplash.com/photo-1586367751368-99141fd186a0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      assigneeName: "Frank Abel",
      reviewers: [
        "https://images.unsplash.com/photo-1542513217-0b0eedf7005d?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
        "https://images.unsplash.com/photo-1546672741-d327539d5f13?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
        "https://images.unsplash.com/photo-1587492520470-8cea42e7b7fe?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      ],
    };
  },
  components: {
    PierCardHeading,
    PierCardDate,
    PierCardMiniProfile
  },
};
</script>