var axios = require('axios');
var _ = require('lodash');

var ROOT_URL = 'https://www.googleapis.com/youtube/v3/search';

module.exports = function (options, callback, error) {
  if (!options.key) {
    throw new Error('Youtube Search expected key, received undefined');
  }

  var params = {
    part: 'snippet',
    key: options.key,
    q: options.term,
    type: 'video',
    maxResults: options.maxResults
  };

  axios.get(ROOT_URL, { params: params })
    .then(function(response) {
      const results = response.data.items;
    //   return console.log(results);
        const search_results = _.map(results, (res) => {
        const thumbs = res.snippet.thumbnails;

        let result = {
          channelId : res.snippet.channelId,
          channel : res.snippet.channelTitle,
          title : res.snippet.title,
          subtitle : res.snippet.channelTitle,
          id : res.id.videoId,
          url: `https://www.youtube.com/watch?v=${res.id.videoId}`,
          embed_url: `https://youtube.com/embed/${res.id.videoId}`
        };

        result.bg = thumbs.default.url;
        if(thumbs.high)
            result.image = thumbs.high.url;

        return result;
      });
      if (callback) { callback(_.filter(search_results, item => {
          return item.title && item.title.length && item.image && item.image.length
      })) }
    })
    .catch(function(err) {
      console.error(err);
      if (error) { error(err) }
    });
};
