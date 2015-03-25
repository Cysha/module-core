@if (isset($id))

<textarea id="{{ $id }}" name="{{ $id }}" class="form-control" rows="10">{{ $content or '' }}</textarea>

<script type="text/javascript">
(function () {
    jQuery('textarea#{{ $id }}').pagedownBootstrap({
        'sanatize': false
    });
})();
</script>
@endif
