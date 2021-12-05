export default {
    FETCHING_MODELS(state, status){
        state.fetchingModels = status;
    },
    SET_MODELS(state, models){
        state.models = models;
    },
    SET_MODEL_BEING_EDITED(state, modelId){
        state.modelBeingEditedId = modelId;
    },
    SAVING_MODEL(state, status){
        state.savingModel = status;
    },
    UPDATE_MODEL_DETAILS(state, {modelId, updatedDetails}){
        if(!state.models)
            state.models = [];

        state.models = state.models.map((model) => {
            if(model._id === modelId){
                model = {
                    ...model,
                    ...updatedDetails
                }
            }

            return model;
        });
    },
}