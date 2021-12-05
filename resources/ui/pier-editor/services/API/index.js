import { post, get, patch, remove, mock } from "./setup";
import { getToken } from "../auth";

export const fetchModels = async () => {
    const token = await getToken();
    return get("/model", token);
};

export const insertModel = async (data) => {
    const token = await getToken();
    return post("/model", data, token);
};

export const saveModelSettings = async (modelName, settings) => {
    const token = await getToken();
    // console.log("Save settings", modelName, settings);
    return patch(`/model/${modelName}/settings`, settings, token);
};

export const saveModelDisplayField = async (modelName, display_field) => {
    const token = await getToken();
    return patch(`/model/${modelName}`, {display_field}, token);
};

export const saveNewModelField = async (modelName, payload) => {
    const token = await getToken();
    return patch(`/model/${modelName}/addField`, payload, token);
};

export const saveModel = async (data) => {
    const token = await getToken();
    return patch("/model", data, token);
};

export const deleteModel = async (modelId) => {
    const token = await getToken();
    return remove(`/model/${modelId}`, token);
};

export const populateModel = async (modelName) => {
    const token = await getToken();
    return post(`/model/${modelName}/populate`, null, token);
};

export const browseModel = async (modelName) => {
    const token = await getToken();
    return get(`/model/${modelName}/browse`, token);
};