<?php

namespace Cms\Modules\Core\Http\Controllers\Frontend;

class PagesController extends BaseCoreController
{
    public function getHomepage()
    {
        $this->setLayout('homepage');

        // dd(\Auth::user()->has2fa);
        return $this->setView('home.index', [], 'theme');
    }

    public function test()
    {
        return $this->outputMethod();
    }
}
