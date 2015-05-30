<?php namespace Cms\Modules\Core\Composers;

use Teepluss\Theme\Contracts\Theme;

class EditorPagedown
{
    protected $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     *
     */
    public function compose($view)
    {
        $this->theme->asset()->add(
            'pagedown-bootstrap.js',
            'modules/core/editors/pagedown-bootstrap/jquery.pagedown-bootstrap.combined.min.js'
        );
        $this->theme->asset()->add(
            'pagedown-bootstrap.css',
            'modules/core/editors/pagedown-bootstrap/jquery.pagedown-bootstrap.css'
        );
    }
}
