<?php
return [
    // 开启调试
    'debug'=>1,
    // 输出时压缩
    'compress'=>0,
    // 缓存设置
    'cache'=>[
    // 开启缓存
        'enabled'=>0,
    // 允许缓存的页面
        'allow'=>[
    // ['控制器文件夹名'=>['控制器类型'=>['控制器方法名',..]]]
            'public'=>['home'=>['index']]
        ],
    ]
];