<?php
//bns24 04/14/24

function fetch_quote($yearnum){
    $result = get("https://numbersapi.p.rapidapi.com/$yearnum/year", "NUMBER_API_KEY", ["fragment" => true, "json" => true]);

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    
    return $result;
}

?>