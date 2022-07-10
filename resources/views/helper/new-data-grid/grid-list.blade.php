<div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach ($data as $item)
        @include('pier::helper.new-data-grid.grid-card')
    @endforeach
</div>
