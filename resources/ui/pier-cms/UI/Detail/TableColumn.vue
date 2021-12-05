<style scoped>
    .pier-col-image{
        display: inline-block;
        width: 200px;
        min-width: 200px;
        height: 160px;
        border-radius: 5px;
        object-fit: cover;
        object-position: center;
    }

    .pier-col-image.youtube{
        height: 45px;
    }

    .pier-col-image.face{
        width: 50px;
        min-width: 50px;
        height: 50px;
        border-radius: 50%;
    }
</style>
<script>

import formatDate from 'date-fns/format';
import parseDate from 'date-fns/parse';
import { getMapLocation, getYouTubeVideoIdFromUrl } from "../../Utils";
export default {
    name: "TableColumn",
    props: [ 'field', 'value' ],
    render (h) {
        function renderColumn(value, type, meta){
            switch (type) {
                case 'image':{
                    let className = "pier-col-image";
                    if(meta && meta.face)
                        className += " face";

                    return <img class={className} src={value} />;
                }
                
                case 'video':{
                    const videoId = getYouTubeVideoIdFromUrl(value);
                    const src = `https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`;
                    return (
                        <a target="_blank" href={value}
                            style="border-radius: 5px;"
                            class="inline-flex relative overflow-hidden">
                            <img class="pier-col-image youtube" src={src} />

                            <span class="bg-black bg-opacity-50 text-red-500 absolute inset-0 flex items-center justify-center z-10">
                                <svg fill="currentColor" width="20px" viewBox="0 0 24 24">
                                    <rect fill="#fff" x="5" y="5" width="12" height="12" />
                                    <path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                                </svg>
                            </span>
                        </a>
                    );
                }
                
                case 'rating':{
                    const fullRating = Array(parseInt(meta.outOf)).fill(2).map((_, index) => index + 1);
                    const stars = fullRating.map((index) => {
                        if(value >= index)
                            return <svg fill="#e9b531" height="24" width="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>;
                        else{
                            if((index - value) < 1)
                                return <svg fill="#e9b531" height="24" width="24" viewBox="0 0 24 24" enable-background="new 0 0 24 24"><path d="M22,9.24l-7.19-0.62L12,2L9.19,8.63L2,9.24l5.46,4.73L5.82,21L12,17.27L18.18,21l-1.63-7.03L22,9.24z M12,15.4V6.1 l1.71,4.04l4.38,0.38l-3.32,2.88l1,4.28L12,15.4z"/></svg>;

                            return <svg fill="#e9b531" height="24" width="24" viewBox="0 0 24 24"><path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"/></svg>;
                        }
                    });

                    return (
                        <span class="inline-flex items-center">
                            { stars } 
                            <span class="ml-2">
                                ( { value } )
                            </span>
                        </span>
                    );
                }
                    
                case 'boolean':{
                    if(value === 1)
                        return <svg class="inline-block text-green-500" fill="currentColor" height="24" width="24" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    else
                        return <svg class="inline-block text-red-500" fill="currentColor" height="24" width="24" viewBox="0 0 24 24"><path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                }

                case 'status':{
                    const statusColor = meta.availableStatuses.find(({name}) => name === value).color;

                    return (
                        <span class="rounded-full px-3 py-1 text-white uppercase text-sm tracking-wider"
                            style={{background: statusColor}}>
                            { value }
                        </span>
                    );
                }
                
                case 'color':{
                    return (
                        <div class="inline-block rounded-sm w-10 h-6 border border-gray-300" style={{background: value}}>&nbsp;</div>
                    )
                }
                
                case 'date':{
                    const dbDate = value.split(" ").shift().trim();
                    let dateString = formatDate(new Date(dbDate), 'do MMM yyyy');

                    if(meta && meta.includeTime){
                        const parsedDate = parseDate(
                            value,
                            "yyyy-MM-dd HH:mm:ss",
                            new Date()
                        );

                        dateString = formatDate(new Date(parsedDate), 'do MMM yyyy, hh:mm a');
                    }

                    return (
                        <span>
                            { dateString }
                        </span>
                    );
                    
                }
                    
                case 'link':
                case 'file':
                    return (
                        <a class="text-blue-500" target="_blank" href={value}>
                            {value}
                        </a>
                    );
                case 'location':
                    if(!value) return null;
                    return (
                        <img src={getMapLocation(value, 1080, 720)} />
                    );
                
                case 'string':
                case 'long text':
                    return <span>{value}</span>;
                
                default:
                    return <span>{value}</span>   
            }
        }

        let {type, meta} = this.field;
        let value = this.value;

        let className = "pier-td";
        className += ` ${type}`;
        className += meta ? ` ${meta.type}` : '';

        let column;

        if(type === 'reference'){
            type = meta.type;
            value = value[meta.field]
        }
        
        if(type === 'multi-reference'){
            if(!value || !value.length){
                return (
                    <td class={className}>
                        No { label }
                    </td>
                );
            }
            
            else if(meta.type === 'image' && meta.face){
                return (
                    <td class={className}>
                        { value.slice(0,3).map(item => renderColumn(item[meta.field], meta.type, meta)) }
                        
                        { value.length > 3 && (
                            <span class="pl-2">+{value.length - 3}</span>
                        ) }
                    </td>
                );
            }

            return (
                <td class={className}>
                    {value.length} { label }
                </td>
            );
        }

        return (
            <td class={className}>
                { renderColumn(value, type, meta) }
            </td>
        );
    }
}
</script>