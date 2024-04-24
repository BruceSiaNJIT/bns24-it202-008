<?php

function redirect($path){
    if(!headers_sent()){
        die(header("Location: " . get_url($path)));
    }
    
    //javascript redirect
    echo "<script>window.location.href='" . get_url($path) . ";</script>";

    //metadata redirect
    echo "<noscript><meta http-equiv=\"refresh\" content=\"0;url=" . get_url($path) . "\"/></noscript>";
    die();
}