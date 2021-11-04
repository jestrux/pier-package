<div id="pierForm"></div>

<script>
    window.pierModel = {
        ...({!! $model !!}),
        fields: {!! $model->fields !!},
        settings: {!! $model->settings !!}
    }

    window.pierModelValues = {!! collect($values == null ? [] : $values)->toJson() !!};

    window.pierRedirectTo = "{{$redirectTo}}";
</script>