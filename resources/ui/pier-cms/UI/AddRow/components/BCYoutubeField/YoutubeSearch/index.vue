<style scoped>
#YoutubeSearchWrapper{
    box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.3);
    position: relative;
  }

  #YoutubeSearchWrapper:after{
      position: absolute;
      top: 43px;
      left: 0;
      content: 'Press enter to search';
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      color: #aaa;
      display: block;
      font-size: 0.8em;
  }

  #YoutubeSearchWrapper:not(.typing):after{
    opacity: 0;
  }
  
  #YoutubeSearchWrapper.typing{
    margin-bottom: 1.7em;
  }
  
  #YoutubeSearchWrapper input{
    background: transparent;
    -webkit-appearance: none;
    box-sizing: border-box;
    font-size: 1.1em;
    width: 100%;
    border: none;
    resize: none;
    outline: none;
    box-shadow: none;
    margin: 0;
  }

  #emptyMessage{
    padding: 0.5em;
    padding-top: 0;
    color: #777;
    margin-left: 0.1em;
    font-size: 0.9em;
  }

  #movers{
    position: absolute;
    top: 5px;
    height: 38px;
    right: 0;
    padding: 0 0.8em;
    display: flex;
    align-items: center;
  }

  button{
    text-transform: uppercase;
    letter-spacing: 0.15em;
    border: 1px solid #999;
    border-radius: 3px;
    padding: 0.25em 0.8em;
    background: transparent;
    outline: none;
    font-size: 0.7rem;
  }

  button:not(.clickable){
    pointer-events: none;
    opacity: 0.4;
  }

  #YoutubeSearchWrapper #results{
    padding: 0 0.5em;
    margin-top: 0.4em;
    margin-right: -0.5em;
  }

  #YoutubeSearchWrapper #results > div{
    display: flex;
    align-items: flex-start;
    justify-content: center;
    flex-wrap: wrap;
    position: relative;
  }

  #YoutubeSearchWrapper .video-list-item{
    position: relative;
    flex-shrink: 0;
    flex-basis: 50%;
    width: 50%;
    position: relative;
    cursor: pointer;
    transition: transform 0.15s ease-out;
    z-index: 2;
    position: relative;
    margin-bottom: 0.5em;
  }

  #YoutubeSearchWrapper .video-list-item-thumb{
    width: 95%;
    width: calc(100% - 0.5em);
    height: 150px;
    overflow: hidden;
    display: flex;
    align-items: center;
    background: #333;
    border-radius: 2px;
  }

  #YoutubeSearchWrapper .video-list-item img{
    width: 100%;
    height: 200px;
    object-fit: cover;
    object-position: center;
  }

  #YoutubeSearchWrapper .video-list-item-caption{
    padding: 0.6em 0;
    background: rgba(255, 255, 255, 0.95);
    overflow: hidden;
    line-height: 1.1em;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
  }

  #YoutubeSearchWrapper .video-list-item-caption span{
    font-size: 0.9em;
  }

  /* #YoutubeSearchWrapper #movers{
    position: absolute;
    top: 0;
    height: 38px;
    right: 0;
    padding: 0 0.8em;
    display: flex;
    align-items: center;
  }

  #YoutubeSearchWrapper button{
    text-transform: uppercase;
    letter-spacing: 0.15em;
    border: 1px solid #999;
    border-radius: 3px;
    padding: 0.25em 0.8em;
    background: transparent;
    outline: none;
    font-size: 0.7rem;
  }

  #YoutubeSearchWrapper button:not(.clickable){
    pointer-events: none;
    opacity: 0.4;
  } */
</style>

<template>
  <div id="YoutubeSearchWrapper" :class="{'typing': !fetched && typing}">
    <input type="text" v-model="query" 
      placeholder="Enter keywords and press enter"
      @keydown.enter.prevent=""
      @keyup="startedTyping($event.target.value)"
      @keyup.enter="searchYoutube($event.target.value)"/>
    
    <div id="movers" v-if="fetched && results.length > perPage">
      <button :class="{'clickable' : page > 1}"
        @click="page = page - 1">Prev</button>
      &emsp;
      <button :class="{'clickable' : results.length > page * perPage}"
        @click="page = page+1">Next</button>
    </div>

    <div id="results" v-if="!typing && fetched && results.length">
      <div>
        <template v-for="(video, index) in results">
          <div :title="video.title" class="video-list-item" 
            v-if="index >= (page - 1) * perPage && index < page * perPage"
            :key="index"
            @click="selectVideo(video.url)">
            <div class="video-list-item-thumb">
                <img :src="video.image" :alt="video.title" />
            </div>
            <div class="video-list-item-caption">
              <span>{{video.title.substr(0, 30) + (video.title.length > 30 ? '...' : '')}}</span>
            </div>
          </div>
        </template>
      </div>
    </div>

    <div id="emptyMessage" v-if="!typing && (fetching || fetch_error || (fetched && !results.length))">
      <span v-if="fetching">
        Fetching videos....
      </span>
      <span v-else-if="fetch_error" style="color: #dd5555">
        An error occured fetching videos, check your network connection and try again.
      </span>
      <span v-else-if="!results.length">
        No results found for <strong>{{query}}</strong>
      </span>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import YTSearch from './Search'
  import results from './results'

  var self;
  
  export default {
    props: {
      perPage: {
        type: String,
        default: 10
      },
      apiKey: String
    },
    data: function() {
      return{
        fetching: false,
        fetched: false,
        fetch_error: false,
        query: "",
        page: 1,
        results: [],
        typing: false
      }
    },
    methods: {
      startedTyping(val){
        this.typing = val;
        this.fetched = false;
        this.results = [];
      },
      searchYoutube(q){
        this.fetching = true;
        this.fetched = false;
        this.fetch_error = false;
        this.results = [];
        this.page = 1;
        this.typing = false;

        YTSearch({key: this.apiKey, term: q, maxResults: 16}, 
          videos => {
            this.fetched = true;
            this.results = videos;
            this.fetching = false;
            // console.log("Api result", videos);
          },
          err => {
            this.fetched = true;
            this.fetching = false;
            this.fetch_error = true;
            console.log("Api error", err);
          })
      },
      selectVideo(url){
        this.$emit("input", url)
      }
    }
  }
</script>