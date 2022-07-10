@if ($grouped)
    @foreach ($data as $group => $data)
        <div class="mb-8">
            <h3 class="text-xl leading-none font-semibold mb-3">
                {{$group}}
            </h3>

            @include('pier::helper.new-data-grid.grid-list')
        </div>
    @endforeach
@else
    @include('pier::helper.new-data-grid.grid-list')
@endif
