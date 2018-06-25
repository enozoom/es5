<?php
namespace app\libraries\Controllers;
/**
 * 用于微信分享的控制器
 * 会自动载入微信分享必要数据到模板页，__data__()方法。
 * 模板页会加载分享代码，view()方法。
 * demo:
<?php
  namespace app\controllers\esweb;
  use app\libraries\Controllers\WechatShareController;
  final class Testshare extends WechatShareController{
     protected $shareMethods = ['index'];
     public $share_img = '默认分享时的方图';
     public function index(){
       $this->title = '分享时的标题';
       $this->description = '分享时的描述';
       $this->share_img = '分享时的方图';
       $this->view();
     }
  }
 *
 */
use ES\Core\Controller\HtmlController;
use ES\Libraries\HTML\HTML5;

class WechatShareController extends HtmlController
{
    // 支持分享的页面
    protected $shareMethods = [];
    // 分享时的方图，支持不写协议域名部分
    public $share_img;
    /**
     * 输出到页面的变量们
     * @param array $data
     */
    protected function __data__(array &$data)
    {
        parent::__data__($data);
        if(in_array($this->cmdq->m, $this->shareMethods) && !empty($this->share_img)){
            $wechat = new \app\libraries\wechat\Share();
            $share = $wechat->shareData();
            $share['img'] = $this->baseUrl($this->share_img);
            $share['link'] = $this->currentURL();
            $share['desc'] = empty($data['share_desc'])?(empty($data['description'])?'':$data['description']):$data['share_desc'];
            $share['title'] = empty($data['share_title'])?(empty($data['title'])?'':$data['title']):$data['share_title'];
            $data['share'] = $share;
        }
    }

    /**
     * 快捷视图
     * 默认装入<head>标签中的数据
     * 装入以控制器.方法名 命名的js,css文件，在文件存在的情况下
     *
     * @param array $data           需要到view页面的变量
     * @param bool $hf               开启头尾
     * @param string $layout_dir  页面通用头尾文件夹，废弃deprecated
     * @return void
     */
    protected function view(array $data=[],bool $hf=TRUE,string $layout_dir='')
    {
        $file = "html/{$this->cmdq->d}/{$this->cmdq->c}/{$this->cmdq->m}";
        $this->__data__($data);
        $H5 = (new HTML5())->init($data);
        $H5->header();
        $this->load->view($file,$data);
        empty($data['share']) || $this->wechatHtml($data['share']);
        $H5->footer();
    }

    private function wechatHtml($share){
        extract($share);
echo <<<HTML
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
wx.config({
    debug: false,
    appId: '{$appId}',
    timestamp: '{$timestamp}',
    nonceStr: '{$nonceStr}',
    signature: '{$signature}',
    jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage']
});
wx.ready(function(){
    var tli = {title: '{$title}', link: '{$link}', imgUrl: '{$img}'}
    wx.onMenuShareTimeline(tli);
    tli.desc = '{$desc}';
    wx.onMenuShareAppMessage(tli);
    document.querySelector('audio').play();
})
</script>
HTML;
    }
}
