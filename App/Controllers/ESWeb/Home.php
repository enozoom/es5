<?php
namespace App\Controllers\ESWeb;

use ES\Core\Controller\HtmlController;
use ES\Core\Toolkit\ConfigStatic;

final class Home extends HtmlController
{
    public $css = 'public.base.min';
    public function index(){
        $this->view(['v'=>substr(ConfigStatic::getConfig('version','Param'),0,4)]);
    }
}