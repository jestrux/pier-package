import Vue from 'vue';
import vuex from 'vuex';

Vue.use(vuex);

import * as actions from './actions';
import state from './state';
import mutations from './mutations';

export default new vuex.Store({
  state,
  actions,
  mutations,
  getters: {
    selectedModel: state => {
      if(!state.selectedModelName || !state.models)
        return null;

      let model = state.models.find(({name}) => name === state.selectedModelName);
      
      try {
        const modelFields = JSON.parse(model.fields);
        if(model && model.fields)
          model.fields = modelFields;

        const modelSettings = JSON.parse(model.settings);
        if(model && model.settings)
          model.settings = modelSettings;
      } catch (error) {
        
      }
        
      return model;
    },
    selectedRecord: state => {
      if(!state.selectedRecordId || !state.records)
        return null;

      return state.records.find(({_id}) => _id === state.selectedRecordId)
    }
  }
})