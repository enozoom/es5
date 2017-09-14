# ES5 Enozoomstudio Framework 5

## ES5包结构

### App 开发包

> App 
>> Controllers 控制器们  
>>> Common 公共控制器文件夹  
>>>> Min.php 处理css,js控制器类  
>>>  
>>> ESWeb 默认控制器文件  
>>>> Home 默认控制器  
>> 
>> Models 模型们  
>> Views 视图们  
>>> errors 错误页面  
>>>> 403.php 非法访问  
>>>> 404.php 页面不存在  
>>>> 503.php 系统错误  
>>>  
>>> html 前端视图们  
>>>> esweb 【小写】与控制器文件夹对应  
>>>>> home 【小写】与控制器类对应  
>>>>>> index.php 与当前控制器方法对应  

### config 配置文件

> config  
>> config.php 基本配置  
>> databse.php 数据库相关  
>> param.php 常用参数  
>> route.php 路由配置  

### ES 核心包

> ES  
>> Core 核心  
>>> Cache 缓存 PSR实现类  
>>> Controller 控制器基类  
>>>> ControllerAbstract.php 控制器基类  
>>>> DataController.php 数据API控制器基类  
>>>> HtmlController.php HTML页面控制器基类  
>>>  
>>> Database 数据库类  
>>> Hook 加载处理  
>>>> HookInterface.php  
>>>> SystemHook.php 框架、控制器初始化过程中的嵌入操作  
>>>  
>>> Http 请求响应  
>>> Load 为控制器加载辅助类库  
>>> Log 日志 PSR实现类  
>>> Model 数据库模型类处理  
>>> Route 路由处理  
>>> Toolkit 工具库   
>>>> AryStatic.php 数组操作  
>>>> AuthTrait.php 加密操作  
>>>> CapchaStatic.php 验证码相关  
>>>> ConfigStatic.php 配置文件读写  
>>>> FileStatic.php 文件的操作  
>>>> HtmlStatic.php 生成或处理HTML  
>>>> NumberStatic.php 数字处理  
>>>> TimeStatic.php 时间操作  
>>>> XssStatic.php 防御  
>>  
>> Library 类库  
>> enozoomstudio.php 初始化框架  

## logs 日志

> logs  
>> alert 报警级别  
>> debug 调试级别  
>> error 错误级别  

## public 入口

> public
>> theme 主题  
>>> css  
>>>> public.base.min.css 公共样式  
>>>> esweb.home.index.css 【小写】控制器文件夹.控制器类.控制器方法.css
>>>
>>> js  
>>
>> uploads 上传文件夹  
>> index.php 执行入口

## vendor 第三方及类加载器

> vendor  
> composer.json  
> composer.lock  
> README.md  
