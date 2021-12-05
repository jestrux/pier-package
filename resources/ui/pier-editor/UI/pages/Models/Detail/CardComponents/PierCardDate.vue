<template>
  <div class="mb-3 flex items-center">
    <span class="text-gray-500">
      <svg v-if="icon === 'event'" fill="currentColor" class="mr-3 w-5" viewBox="0 0 24 24">
        <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
      </svg>

      <svg v-else fill="currentColor" class="mr-3 w-5" viewBox="0 0 24 24">
        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/><path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
      </svg>
    </span>
    <span class="text-lg text-gray-700">
      {{ dateString }}
    </span>
  </div>
</template>

<script>

import formatDate from 'date-fns/format';
import parseDate from 'date-fns/parse';

export default {
  name: "PierCardDate",
  props: {
    icon: {
      type: String
    },
    date: {
      type: String
    },
    startDate: {
      type: String,
      default: "2020-07-22"
    },
    startTime: {
      type: String,
      default: "16:30:00"
    },
    endDate: {
      type: String,
      default: "2020-07-22"
    },
    endTime: {
      type: String,
      default: "18:00:00"
    }
  },
  computed: {
    dateString(){
      if(this.date && this.date.length)
        return this.formattedDate(this.date.split(" ")[0]);

      let dateString = "";
      let { startDate, startTime, endDate, endTime } = this;
      const start = {};
      const end = {};

      if(startDate && this.formattedDate(startDate))
        start.date = this.formattedDate(startDate);
        
      if(startTime && this.formattedTime(startTime))
        start.time = this.formattedTime(startTime);
      
      if(endDate && this.formattedDate(endDate))
        end.date = this.formattedDate(endDate);

      if(endTime && this.formattedTime(endTime))
        end.time = this.formattedTime(endTime);


      if(start.date){
        if(startDate === endDate || !endDate || !end.date){
          dateString = start.date;
          if(start.time){
            dateString += `, ${start.time}`;
            if(end.time)
              dateString += ` - ${end.time}`;
          }
        }
        else if(startDate.split('-')[1] == endDate.split('-')[1]){
          dateString = start.date.split(" ")[0] + " - ";
          const splitEndDate = end.date.split(" ");
          if(splitEndDate.length === 3){
            splitEndDate.splice(1, 1);
            dateString += splitEndDate.join(" ").trim()
          }
          else
            dateString += splitEndDate.slice(0, 2).join(" ").trim();


          if(start.time)
            dateString += `, from ${start.time}`;
        }
        else{
          dateString = `${start.date} - ${end.date}`;

          if(start.time)
            dateString += `, from ${start.time}`;
        }
        
        return dateString;
      }
    },
  },
  methods:{
    formattedDate(date){
      try {
        const parsedDate = parseDate(
          date,
          "yyyy-MM-dd",
          new Date()
        );

        let formattedDate = formatDate(new Date(parsedDate), 'do MMM yyyy');
        return formattedDate.replace(` ${new Date().getFullYear()}`, '');
      } catch (error) {
        console.log(`Error formatting: ${date}`, error);
        return null;
      }
    },
    formattedTime(time){
      try {
        const parsedDate = parseDate(
          time,
          "HH:mm:ss",
          new Date()
        );

        return formatDate(new Date(parsedDate), 'hh:mm a');
      } catch (error) {
        return false;
      }
    }
  }
};
</script>