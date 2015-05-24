<?php namespace Cms\Modules\Core\Http\Controllers\Frontend;

class PagesController extends BaseCoreController
{

    public function getHomepage()
    {
        $this->setLayout('homepage');

        return $this->setView('home.index', [], 'theme');
    }

    public function test()
    {
        return $this->outputMethod();
    }
}
