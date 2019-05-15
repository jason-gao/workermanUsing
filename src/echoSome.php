<?php

namespace workermanUsing;

class echoSome{

    public function echoStr($str = '11'){
        if(empty($str)){
            $str = date('Y-m-d H:i:s');
        }

        echo $str."\n";
    }
}