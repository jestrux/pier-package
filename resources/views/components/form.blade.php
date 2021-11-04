<div id="pierForm"></div>

<script>
    window.pierModel = {
        ...({!! $model !!}),
        fields: {!! $model->fields !!},
        settings: {!! $model->settings !!}
    }

    @if($values != null)
        window.pierModelValues = {!! collect($values)->toJson() !!};
    @endif

    window.pierRedirectTo = "{{$redirectTo}}";
</script>