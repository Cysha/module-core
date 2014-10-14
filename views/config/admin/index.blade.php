<div class="row">
    <div class="col-md-2"> @include('core::config.admin.nav') </div>
    <div class="col-md-7">
    {{ Former::horizontal_open( URL::route('admin.config.store') ) }}
        {{ Form::Config('core::app.site-name')->label('Site Name') }}

        {{ Form::Config('core::app.google-analytics')->label('Google Analytics Code') }}

        {{ Form::Config('core::app.force-secure', 'radio')->radios(['Yes' => ['value' => 'true'], 'No' => ['value' => 'false']])->label('Force HTTPS?') }}
        {{ Form::Config('core::app.debug', 'radio')->radios(['Yes' => ['value' => 'true'], 'No' => ['value' => 'false']])->label('Enable Debug?') }}

        <button class="btn-labeled btn btn-success pull-right" type="submit">
            <span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span> Save
        </button>

    {{ Former::close() }}
    </div>
</div>
