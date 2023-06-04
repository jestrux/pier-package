<style>
    :root {
        --primary-color: {{ env('APP_COLOR') ?? '#2c5282' }};
    }
</style>

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