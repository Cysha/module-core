@if (isset($id))

<textarea id="txt_{{ $id }}" name="{{ $id }}" class="form-control" rows="10">{!! $content or '' !!}</textarea>

@if ($errors->has($id))

    {!! $errors->first($id, '<span class="help-block">:message</span>') !!}

@endif


<script type="text/javascript">
(function () {
    jQuery('textarea#txt_{{ $id }}').pagedownBootstrap({
        'sanatize': false
    });
})();
</script>
@endif
