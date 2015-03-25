<?php namespace Cysha\Modules\Core\Composers;

use Theme;

class Editors_Pagedown
{
    /**
     *
     */
    public function compose($view)
    {
        Theme::asset()->add(
            'pagedown-bootstrap.js',
            'packages/module/core/assets/editors/pagedown-bootstrap/jquery.pagedown-bootstrap.combined.min.js',
            ['application.js']
        );
        Theme::asset()->add(
            'pagedown-bootstrap.js',
            'packages/module/core/assets/editors/pagedown-bootstrap/jquery.pagedown-bootstrap.css',
            ['theme.css']
        );
    }
}
