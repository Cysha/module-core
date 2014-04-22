<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

use Cysha\Modules\Core as Core;
use Cysha\Modules\Core\Controllers\Admin\BaseAdminController as BAC;

class BaseConfigController extends BAC
{
    public function __construct(\URL $url, \Input $input, \Redirect $redirect)
    {
        parent::__construct();

        $this->url = $url;
        $this->input = $input;
        $this->redirect = $redirect;

        $this->theme->breadcrumb()->add('Configuration Manager', $this->url->route('admin.config.index'));
    }

    public function postStoreConfig()
    {
        $settings = Input::except('_token');

        $configModel = new Core\Models\DBConfig;

        $failed = array();
        foreach ($settings as $setting => $value) {
            $setting = str_replace('_', '.', $setting);

            $settingInfo = $configModel->explodeSetting($setting);

            // check to see if we already have this setting going
            $DBConfig = Core\Models\DBConfig::where('environment', $settingInfo['environment']);
            if (isset($settingInfo['group'])) {
                $DBConfig->where('group', $settingInfo['group']);
            }

            if (isset($settingInfo['item'])) {
                $DBConfig->where('item', $settingInfo['item']);
            }

            if (isset($settingInfo['namespace'])) {
                $DBConfig->where('namespace', $settingInfo['namespace']);
            }
            $DBConfig = $DBConfig->get()->first();

            // if we have a config row already, update the value
            if (count($DBConfig)) {
                $DBConfig->value = $value;
                $saved = $DBConfig->save();

            // else create a new one
            } else {
                $DBConfig = new Core\Models\DBConfig;
                $saved = $DBConfig->set($setting, $value);
            }

            // if the save failed, add it to the array to be passed back
            if ($saved === false) {
                $failed[] = $setting;
            }
        }

        if (count($failed)) {
            return $this->redirect->back()->withError('Config Save partially failed. The following keys could not be saved: <ul><li>'.implode('</li><li>', $setting).'</li></ul>');
        }

        return $this->redirect->back()->withInfo('Config Saved');
    }
}