<?php
namespace app\common\behavior;
/**
 * 配置控制器，控制路由的交由谁处理
 * 情况1： /admin.php   交由 "admin/controller/Index/index"
 * 情况2： /admin.php/模块名 (模块名称没在 module 配置文件中配置)  交由 "模块/admin/Index/index"
 * 情况3： /admin.php/模块名 (模块名称在 module 配置文件中配置)  交由 "模块/controller/Index/index"
 * 情况4： /index.php/admin  (交由后台路由处理) admin/controller/Index/index
 * 情况5： /index.php/模块名 (模块名称没在 module 配置文件中配置) 交由 "模块/home/Index/index"
 * 情况6： /index.php/模块名 (模块名称在 module 配置文件中配置) 交由 "模块/Welcome/Index/index"
 */
class Config
{
    public function run(&$params){
        $module = '';
        //获取当前的模块名称
        $dispatch = request()->dispatch();
        if(isset($dispatch['module'])){
            $module = $dispatch['module'][0] ;   //获取当前的模块名称
        }
        //获取入口目录  /admin.php 后台入口    /index.php前台入口
        $base_file = request()->baseFile();
        $base_dir  = substr($base_file,0,strripos($base_file,'/')+1);
        define('PUBLIC_PATH',$base_dir.'public/');
        //视图输入内容替换
        $view_replace_str = [
            //静态资源目录
            '__STATIC__'  =>  PUBLIC_PATH.'static' ,
            //文件上传目录
            '__UPLOADS__' =>  PUBLIC_PATH.'uploads' ,
            //JS插件目录
            '__JS__'      =>  PUBLIC_PATH.'static/libs' ,
            // 后台CSS目录
            '__ADMIN_CSS__' => PUBLIC_PATH. 'static/admin/css',
            // 后台JS目录
            '__ADMIN_JS__'  => PUBLIC_PATH. 'static/admin/js',
            // 后台IMG目录
            '__ADMIN_IMG__' => PUBLIC_PATH. 'static/admin/img',
            // 前台CSS目录
            '__HOME_CSS__'  => PUBLIC_PATH. 'static/home/css',
            // 前台JS目录
            '__HOME_JS__'   => PUBLIC_PATH. 'static/home/js',
            // 前台IMG目录
            '__HOME_IMG__'  => PUBLIC_PATH. 'static/home/img',
        ];
        config('view_replace_str',$view_replace_str);
        //如果定义如果为admin,则修改默认的访问控制器
        if(defined('ENTRANCE') && ENTRANCE=='admin'){
            //说明是访问后台的路由，重定向到/admin.php/admin 路由
            define('ADMIN_FILE',substr($base_file,strripos($base_file,'/')+1));
            if($dispatch['type'] == 'module' && $module == ''){
                header('Location:'.$base_file.'/admin',true,302);exit();
            }
            //如果访问的模块不为空的时候，判断是否是默认控制器的模块，默认访问 模块/controller/Index/index
            if($module != '' && !in_array($module,config('module.default_controller_layer'))){
                //不在数组里面的默认范围  模块/admin/Index/index
                config('url_controller_layer','admin');
                //修改试图默认路径
                config('template.view_path',APP_PATH.$module.'/view/admin/');
            }
        }else{
            //访问后台的另外一种情况  /index.php/admin，跳转给后台路由处理
            if($dispatch['type'] == 'module' && $module == 'admin'){
                header("Location: ".$base_dir.ADMIN_FILE.'/admin', true, 302);exit();
            }
            //当访问模块前端时，默认交给 <模块/home/Index/index>  处理
            if ($module != '' && !in_array($module, config('module.default_controller_layer'))) {
                // 修改默认访问控制器层
                config('url_controller_layer', 'home');
            }
        }
        // 定义模块资源目录
        config('view_replace_str.__MODULE_CSS__', PUBLIC_PATH. 'static/'. $module .'/css');
        config('view_replace_str.__MODULE_JS__', PUBLIC_PATH. 'static/'. $module .'/js');
        config('view_replace_str.__MODULE_IMG__', PUBLIC_PATH. 'static/'. $module .'/img');
        config('view_replace_str.__MODULE_LIBS__', PUBLIC_PATH. 'static/'. $module .'/libs');
    }

}