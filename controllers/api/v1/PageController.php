<?php namespace Cysha\Modules\Core\Controllers\Api\V1;

use Cysha\Modules\Core\Controllers\BaseApiController as BAC;

class PageController extends BAC
{

    public function getIndex()
    {
        return $this->sendOK('ok');
    }
}
