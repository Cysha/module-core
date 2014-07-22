<?php namespace Cysha\Modules\Core\Controllers;

use Url;

class BaseModuleController extends BaseController
{

    /**
     * Controls the layout for a controller
     * @var string
     */
    public $layout = 'cols-2-left';

    public function __construct()
    {
        parent::__construct();

        $this->objTheme->breadcrumb()->add('Home', Url::route('pxcms.pages.home'));
    }
}
