<div class="row">
    <div class="col-md-2"> @include('core::config.admin.nav') </div>
    <div class="col-md-7">
    {{ Former::horizontal_open( URL::route('admin.config.store') ) }}
        {{ Form::Config('core::app.site-name')->label('Site Name') }}


        <button class="btn-labeled btn btn-success pull-right" type="submit">
            <span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span> Save
        </button>

    {{ Former::close() }}
    </div>
</div>
