<?php namespace Cms\Modules\Core\Http\Controllers\Frontend;

class PagesController extends BaseCoreController
{

    public function getHomepage()
    {
        return $this->setView('pages.homepage');
    }

    public function test()
    {
        return $this->outputMethod();
    }
}
