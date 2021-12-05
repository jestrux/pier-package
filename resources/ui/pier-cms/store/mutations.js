export default {
    SET_MODELS(state, models){
        state.models = models;
    },
    SET_SELECTED_MODEL(state, modelName){
        state.selectedModelName = modelName;
    },
    SET_FILTER(state, {key, value}){
        state.modelFilters[key] = value;
    },
    SET_RECORDS(state, {data, pagination}){
        state.records = data;
        if(pagination)
            state.recordsPagination = pagination;
    },
    SET_SELECTED_RECORD(state, recordId){
        state.selectedRecordId = recordId;
    },
    SAVING_RECORD(state, status){
        state.savingRecord = status;
    },
    POPULATING_RECORDS(state, status){
        state.populatingRecords = status;
    },
    FETCHING_RECORDS(state, status){
        state.fetchingRecords = status;
    },
    DELETING_RECORD(state, status){
        state.deletingRecord = status;
    }
}