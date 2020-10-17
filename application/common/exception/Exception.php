<?php

namespace app\common\exception;

use Exception;
class Exception extends Exception {
      static  function Exception($e){
        dump($e);
      }
}