@isset($model)
    <div x-data="{
        open: false,
        toggle() {
            if (this.open) {
                return this.close()
            }
    
            this.$refs.button.focus()
    
            this.open = true
        },
        close(focusAfter) {
            if (!this.open) return
    
            this.open = false
    
            focusAfter && focusAfter.focus()
        }
    }" class="opacity-0 group-hover:opacity-100"
        :class="{ 'opacity-100': open }">
        <div class="flex justify-center">
            <div x-on:keydown.escape.prevent.stop="close($refs.button)"
                x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']"
                :class="{ 'relative z-10': open }">
                <button x-ref="button" x-on:click="toggle()" :aria-expanded="open" :aria-controls="$id('dropdown-button')"
                    type="button"
                    class="cursor-pointer flex items-center justify-center w-7 h-7 hover:bg-neutral-200/50 rounded-full focus:outline-none border border-transparent focus:border-neutral-200"
                    :class="{ 'bg-neutral-200/50': open }">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </button>

                <div x-ref="panel" x-show="open" x-transition.origin.top.left x-on:click.outside="close($refs.button)"
                    :id="$id('dropdown-button')" style="display: none;"
                    class="absolute right-1 w-40 rounded-md bg-white border shadow-md">
                    <a href="{{ url('/admin/upsertModel/' . $model . '/' . $rowId) }}"
                        class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                        Edit
                    </a>

                    <button
                        class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                        onclick="deleteEntry('{{ $rowId }}')">
                        <span class="text-red-600">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endisset
