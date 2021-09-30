<?php
function random_num($length)
{
    $text = "";
    if($length < 6)
    {
        $length = 5;
    }

    $len = rand(5, $length);

    for($i=0; $i < $len; $i++){

        $text .= rand(0,9,);
    }

    return $text;
}
?>