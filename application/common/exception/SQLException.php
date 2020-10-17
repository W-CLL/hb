<?php

namespace app\common\exception;

/**
 * PDO异常处理类
 * 重新封装了系统的\PDOException类
 */
/**
 * PDOException constructor.
 * @param \PDOException $exception
 * @param array         $config
 * @param string        $sql
 * @param int           $code
 */
class SQLException extends BaseException {
    public $code = 404;
    public $msg = '用户不存在';
    public $errorCode = 60000;
/*      public function render(Exception $e)
    {
        if($e){
            dump($e);
        }
        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json($e->getError(), 422);
        }

        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode());
        }

        //TODO::开发者对异常的操作
        //可以在此交由系统处理
        return parent::render($e);
    } */

}