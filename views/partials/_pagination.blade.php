@if (is_object($object) && $object->count())

    {{ $object->links() }}

@endif
