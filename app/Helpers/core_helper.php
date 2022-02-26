<?php 

function pre($data){
    echo "<pre>";print_r($data);
}
function prd($data){
    echo "<pre>";print_r($data);die;
}
function render_date($time, $type="input", $format=""){

    if(strlen(trim(@$time)) === 0){
        return "";
    }
    if (!empty($format)) {

        $date = date($format, $time);
    } else if( $type == "date_time"){

        $date = date("d M Y H:i:s", $time);
    }else if( $type == "input"){
        $date = date("m/d/Y", $time);

    }else{
        $date = date("d M Y", $time);

    }

    return $date;
}

function getCurrency($key){

    $key = $key-1;
    $list=["GBP", "USD", "EUR"];

    return $list[$key];
}
function getStatus($key){

    $key = $key-1;
    $list=["Active", "Completed"];

    return $list[$key];
}

function render_head_text($text){
    $text = str_replace("_", " ", $text);
    $text = ucwords($text);

    return $text;
}    
function getTitleHour($time)
{
    $splitted = explode(" ", $time);
    $meridianFirstletter = @trim(@$splitted[1])[0];
    $splittedTime = array_filter(array_map('trim', explode(":", $splitted[0])), 'strlen');
    $hour = @$splittedTime[0];
    return $hour . $meridianFirstletter;
}
?>