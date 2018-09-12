<?php
namespace app\controllers\esweb;

use ES\Core\Controller\HtmlController;
use ES\Core\Toolkit\ConfigStatic;

final class Home extends HtmlController
{
    public $css = 'public.common.reset';
    public function index(){
        $this->view(['v'=>substr(ConfigStatic::getConfig('version','Param'),0,4)]);
    }
}