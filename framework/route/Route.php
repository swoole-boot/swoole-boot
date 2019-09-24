<?php
namespace boot\route;

use boot\Func;
use cockroach\base\Cockroach;

/**
 * Class Route
 * @package boot
 * @datetime 2019/9/12 13:12
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Route extends Cockroach
{
    /**
     * @var string
     * @datetime 2019/9/12 13:37
     * @author roach
     * @email jhq0113@163.com
     */
    public $funcNamespace = 'app\funcs';

    /**
     * @var string
     * @datetime 2019/9/23 11:41
     * @author roach
     * @email jhq0113@163.com
     */
    public $separator = ':';

    /**func所在根目录，要与funcNamespace对应好
     * @var string
     * @datetime 2019/9/24 13:47
     * @author roach
     * @email jhq0113@163.com
     */
    public $funcPath = APP_PATH.'/funcs';

    /**
     * @var array
     * @datetime 2019/9/24 10:55
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_funcs = [];

    /**
     * @param array $package
     * @return mixed
     * @datetime 2019/9/23 11:30
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function route(array $package);

    /**
     * @param string $dir
     * @param array  $files
     * @return array
     * @datetime 2019/9/24 11:34
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _files($dir, &$files)
    {
        if(is_dir($dir)){
            $childDirs = scandir($dir);

            foreach($childDirs as $childDir){
                if($childDir != '.' && $childDir != '..'){
                    if(is_dir($dir.'/'.$childDir)){
                        $this->_files($dir.'/'.$childDir,$files);
                    }else{
                        array_push($files, $dir.'/'.$childDir);
                    }
                }
            }

            return $files;
	    } else {
            return [];
        }
    }

    /**
     * @return array
     * @datetime 2019/9/24 10:58
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _getAllClass()
    {
        if(empty($this->_funcs)) {
            $files = [];
            $this->_files($this->funcPath,$files);

            //加载所有的funcs
            $pathLength = mb_strlen($this->funcPath);
            array_map(function ($file) use($pathLength){
                if(substr($file,-4) == '.php') {
                    $class = $this->funcNamespace.'\\'.str_replace('/','\\',mb_substr($file,$pathLength+1));
                    array_push($this->_funcs,rtrim($class,'.php'));
                }
            },$files);
        }

        return $this->_funcs;
    }

    /**
     * @return array
     * @datetime 2019/9/24 14:14
     * @author roach
     * @email jhq0113@163.com
     */
    public function getFuncs()
    {
        $funcList = $this->_getAllClass();

        $length = strlen($this->funcNamespace);
        $funcs = [];
        array_map(function($func) use (&$funcs, $length){
            if(class_exists($func)) {
                $funcObj = new $func();
                if($funcObj instanceof Func && $funcObj->autoRegister) {
                    $route = str_replace('\\',$this->separator,substr($func,$length + 1));

                    $seviceId = empty($funcObj->id) ? str_replace($this->separator,'-',$route) : $funcObj->id;

                    $rules = $funcObj->rules();
                    $params = [];
                    foreach ($rules as $rule) {
                        $params = array_merge($params, $rule[0]);
                    }
                    $funcs[ $seviceId ] = json_encode([
                        'route'  => $route,
                        'params' => array_unique($params)
                    ]);
                }
            }

        },$funcList);

        return $funcs;
    }
}