import { post, get, patch, remove, mock } from "./setup";
// import { getToken } from "../auth";

export const getLinkPreview = async (link) => {
    // const token = await getToken();
    return get(`/link_preview/?link=${link}`, null, token);
};

export const populateModel = async (modelName, itemCount = 25) => {
    // const token = await getToken();
    return post(`/model/${modelName}/populate?item_count=${itemCount}`, null, token);
};

export const searchModel = async (modelName, query) => {
    // const token = await getToken();
    return get(`/api/${modelName}/search?q=${query}`, null, token);
};

export const fetchModelRecords = async (modelName, params) => {
    // const token = await getToken();
    let queryParams = "";
    if(params){
        queryParams = [];
        for (const [key, value] of Object.entries(params)) {
            queryParams.push(`${key}=${value}`);
        }
        queryParams = queryParams.join("&");
    }

    const url = `/api/${modelName}?${queryParams}`;
    return get(url, token);
};

export const deleteRecord = async (modelName, rowId) => {
    // const token = await getToken();
    return remove(`/api/${modelName}/${rowId}`, token);
};

export const insertRecord = async (modelName, data) => {
    // const token = await getToken();
    return post(`/api/${modelName}`, data, token);
};

export const updateRecord = async (modelName, data) => {
    // const token = await getToken();
    return patch(`/api/${modelName}/${data._id}`, data, token);
};