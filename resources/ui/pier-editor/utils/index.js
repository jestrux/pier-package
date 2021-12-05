import Vue from 'vue';

export async function handleNetworkError(error, fallbackMessage = "Network error"){
  if(error && error.response && error.response.data)
    showErrorToast(error.response.data);
  else
    showErrorToast(fallbackMessage);
  
  console.log(fallbackMessage, error);
}

export function showErrorToast(message){
  Vue.$toast.error(message, {
    position: 'top-right',
    duration: 3000
  });
}

export function showSuccessToast(message){
  Vue.$toast.success(message, {
    position: 'top-right',
    duration: 3000
  });
}