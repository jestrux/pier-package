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
    modelBeingEdited: state => {
      if(!state.modelBeingEditedId || !state.models)
        return null;

        let model = state.models.find(({_id}) => _id === state.modelBeingEditedId);
      
        try {
          // const modelFields = JSON.parse(model.fields);
          // if(model && model.fields)
          //   model.fields = modelFields;
  
          const modelSettings = JSON.parse(model.settings);
          if(model && model.settings)
            model.settings = modelSettings;
        } catch (error) {
          console.log("Error parsing model: ", error);
        }
          
        return model;
    }
  }
})