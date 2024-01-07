@aware(['model'])

<div class="flex items-center gap-3 border-r border-content/20 mr-6 pr-6">
    Filters:

    <button x-on:click="$wire.set('filters', {...$wire.filters, whereVerified: $wire.filters.whereVerified ? 0 : 1})">
        Verified
    </button>
</div>

{{-- <div class="relative">
    <button id="showFiltersButton" class="rounded-md border-2 py-1 px-2 flex items-center focus:outline-none"
        style="display: flex;"><svg fill="#888" width="24" height="24" viewBox="0 0 24 24">
            <path
                d="M7,6h10l-5.01,6.3L7,6z M4.25,5.61C6.27,8.2,10,13,10,13v6c0,0.55,0.45,1,1,1h2c0.55,0,1-0.45,1-1v-6 c0,0,3.72-4.8,5.74-7.39C20.25,4.95,19.78,4,18.95,4H5.04C4.21,4,3.74,4.95,4.25,5.61z">
            </path>
        </svg><svg viewBox="0 0 24 24" class="h-4 w-4 ml-1">
            <path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z"></path>
        </svg>
    </button>

    <div id="filterDropdown" class="popover-custom shadow-md rounded-md" x-placement="bottom"
        style="display: none; position: absolute; will-change: top, left; top: 36px; left: 32px;">
        <div class="p-3 flex flex-col gap-2">
            <div class="ModelFilterField flex items-center justify-between gap-3">
                <span class="inline-block first-letter:uppercase flex-1">type</span>
                <div class="flex-shrink-0" style="width: 180px;"><select class="border p-1 rounded w-full">
                        <option value="">All</option>
                        <option value="Presidential">Presidential</option>
                        <option value="Standard">Standard</option>
                        <option value="Economy">Economy</option>
                    </select></div>
            </div>
            <div class="ModelFilterField flex items-center justify-between gap-3">
                <span class="inline-block first-letter:uppercase flex-1">complex</span>
                <div class="flex-shrink-0" style="width: 180px;">
                    <div>
                        <div>
                            <div>
                                <div data-position="below" class="autocomplete" style="position: relative;"><input
                                        role="combobox" autocomplete="off" autocapitalize="off" autocorrect="off"
                                        spellcheck="false" aria-autocomplete="list" aria-haspopup="listbox"
                                        aria-owns="autocomplete-result-list-2" aria-expanded="false"
                                        aria-activedescendant="" placeholder="Type to search..."
                                        class="autocomplete-input">
                                    <ul id="autocomplete-result-list-2" role="listbox" class="autocomplete-result-list"
                                        style="position: absolute; z-index: 1; width: 100%; visibility: hidden; pointer-events: none; top: 100%;">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-8 flex items-center px-3 bg-neutral-100 rounded-b-md"><button
                class="underline text-xs font-semibold">Reset filters</button></div>
    </div>
</div> --}}
