export function getYouTubeVideoIdFromUrl(url) {
    url = url.split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
    const youtubeRegex = /(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/;
    if (!youtubeRegex.test(url))
        return null;

    return (url[2] !== undefined) ? url[2].split(/[^0-9a-z_-]/i)[0] : url[0];
}

export async function handleNetworkError(error, fallbackMessage = "Network error") {
    if (error && error.response && error.response.data)
        showErrorToast(error.response.data);
    else
        showErrorToast(fallbackMessage);

    console.log(fallbackMessage, error);
}

export function showErrorToast(message) {
    Vue.$toast.error(message, {
        position: 'top-right',
        duration: 3000
    });
}

export function getMapLocation(location, width, height){
    if(!location || !location.length)
        return null;

    try {
        const { coords, name } = JSON.parse(location);
        if(!coords) return null;
        
        return `
            https://www.mapquestapi.com/staticmap/v5/map?key=WeIoVZDtlQwX3HwGpXiNjk12Ca9eQJUm&center=${coords.reverse().join(',')}&size=${width},${height}&type=dark&zoom=8&marker-7B0099
        `;
    } catch (error) {
        console.log("Can't parse location: ", error);
        return null;
    }
}

export function showSuccessToast(message) {
    Vue.$toast.success(message, {
        position: 'top-right',
        duration: 3000
    });
}

export function toPascalCase(string) {
    return `${string}`
      .replace(new RegExp(/[-_]+/, 'g'), ' ')
      .replace(new RegExp(/[^\w\s]/, 'g'), '')
      .replace(
        new RegExp(/\s+(.)(\w+)/, 'g'),
        ($1, $2, $3) => `${$2.toUpperCase() + $3.toLowerCase()}`
      )
      .replace(new RegExp(/\s/, 'g'), '')
      .replace(new RegExp(/\w/), s => s.toUpperCase());
  }