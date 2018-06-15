<?php
namespace ES\Traits;

use ES\Libraries\HTML\Pagination;
use ES\Core\Toolkit\ConfigStatic;
use ES\Core\Model\ModelAbstract;

trait DataLists {
    /**
     * 获取数据数组和分页HTML
     * @param \ES\Core\Model\ModelAbstract $M
     * @param array $sql [where=>'', select='', orderby='']
     * @param int $per 返回数据行数
     * @param int $offset 偏移数
     * @param string $url 分页上的跳转地址，不含/$per/$offset部分
     * @param bool $dataHref 是否使用data-href代替href属性
     * @return array
     */
    private function __lists(ModelAbstract $M,
                                        int $per = 10,
                                        int $offset = 0,
                                        array $sql = [],
                                        string $url = '',
                                        bool $dataHref=FALSE):array
    {
        $where = empty($sql['where'])?'':$sql['where'];
        $select = empty($sql['select'])?'':$sql['select'];
        $orderby = empty($sql['orderby'])?'':$sql['orderby'];
        $rows = $M->_get($where,$select,$orderby,[$per,$offset]);
        $total = $M->_getTotalnum($where);
        $pagination = '';
        if($total>$per){
            $cmdq = ConfigStatic::getConfigs('Cmdq');
            $url = empty($url)?"{$cmdq->d}/{$cmdq->c}/{$cmdq->m}":'';
            $P = new Pagination();
            $pagination = $P->init($total,$per,$offset,$dataHref,$url);
        }
        return ['rows'=>$rows, 'pagination'=>$pagination];
    }
}

