@if (isset($id))

<textarea id="txt_{{ $id }}" name="{{ $id }}" class="form-control" rows="10">{!! $content or '' !!}</textarea>

<script type="text/javascript">
(function () {
    jQuery('textarea#txt_{{ $id }}').pagedownBootstrap({
        'sanatize': false
    });
})();
</script>
@endif
