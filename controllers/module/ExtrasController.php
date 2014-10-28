<?php namespace Cysha\Modules\Core\Controllers\Module;

use Cysha\Modules\Core\Controllers\BaseModuleController as CoreController;
use Event;
use Cron;

class ExtrasController extends CoreController {

    public function getHomepage()
    {
        return 'Home';
    }

    public function getCron() {

        Event::fire('core::cron-add');

        return Cron::run();
    }

}
