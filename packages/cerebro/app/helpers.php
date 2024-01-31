<?php
    function unify_json(&$json){
        $array = json_decode($json,true);
        foreach($array as $key=>$value){
            if(is_array($value)){
                if(empty($value)){
                    unset($array[$key]);
                }else{
                    unify_json($value);
                }
            }
        }
        $json = json_encode($array);
    }
