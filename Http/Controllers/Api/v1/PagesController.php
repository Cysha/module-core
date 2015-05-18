<?php namespace Cms\Modules\Core\Http\Controllers\Api\V1;

use Cms\Modules\Core\Http\Controllers\BaseApiController as BAC;
use Auth;

class PagesController extends BAC
{

    public function getUser()
    {
        return $this->sendResponse('ok', 200, Auth::user()->transform());
    }

}
