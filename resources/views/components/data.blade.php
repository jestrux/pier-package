<div id="pierComponent{{$instanceId}}"
     pier-data-component="{{$instanceId}}" 
     x-data="pierComponent{{$instanceId}}" 
     x-init="init()"
>
    {!! eval('?>'.Blade::compileString($slot)) !!}
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data("pierComponent{{$instanceId}}", () => ({
            ref: "pierComponent{{$instanceId}}",
            filters: {!! collect($filters)->toJson() !!},
            async updatePierComponentContent(){
                const parentEl = this.$el;
                const activeEl = document.activeElement;
                const activeElementIsWithinBounds = parentEl.contains(activeEl);
                
                const model = "{{$model}}";
                const view = encodeURIComponent(`{{$slot}}`);
                // exclude filters with empty, null or undefined values
                let filters = Object.fromEntries(Object.entries(this.filters).filter(([key, value]) => value !== null && value !== undefined && value.length ));
                filters = Object.fromEntries(Object.entries(filters).map(([key, value], i) => {
                    key = i == 0 ? key : key.replace("where", "andWhere");
                    return [key, value];
                }));
                
                const res = await fetch("/pier/data-refetch", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        model,
                        filters,
                        view: decodeURIComponent(view),
                    })
                });

                const newContent = await res.text();
                document.querySelector("#pierComponent{{$instanceId}}").innerHTML = newContent;

                if(activeElementIsWithinBounds){
                    const activeElPierRef = activeEl.getAttribute("pier-ref");
                    setTimeout(() => {
                        if(activeElPierRef && activeElPierRef.length){
                            this.$nextTick(() => {
                                const newActiveElementInstance = this.$el.querySelector(`[pier-ref="${activeElPierRef}"]`);
                                if(newActiveElementInstance)
                                    newActiveElementInstance.focus();
                            });
                        }
                    }, 100);
                }
            },
            init(){
                this.$watch('filters', _ => {
                    this.updatePierComponentContent();
                });
            }
        }));
    });
</script>