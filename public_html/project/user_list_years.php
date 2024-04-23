<?php
//note we need to go up 1 more directory bns24 04/14/24
require(__DIR__ . "/../../partials/nav.php");

//build search form bns24 04/14/24
$form = [
    ["type" => "number", "name" => "number", "placeholder" => "Year Number", "label" => "Year Number", "include_margin" => false],
    ["type" => "text", "name" => "text", "placeholder" => "Year Info", "label" => "Year Info", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["year" => "Year", "text" => "Text"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];


//bns24 04/14/24
$query = "SELECT id, text, year, type FROM `Numbers` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
$session_data = session_load($session_key);

if($is_clear){
    session_delete($session_key);
    unset($_GET["clear"]);
    die(header("Location: " . $session_key));
}else{
    $session_data = session_load($session_key);
}

if(count($_GET) == 0 && isset($session_data) && count($session_data) > 0){
    if($session_data){
        $_GET = $session_data;
    }
}

if(count($_GET) > 0){
    session_save($session_key, $_GET);

    $keys = array_keys($_GET);

    foreach($form as $k=>$v){
        /*if($v["name"] === "number"){
            $form[$k]["name"] = "year";
            $form[$k]["value"] = $_GET[$v["number"]];
        }*/
        if(in_array($v["name"], $keys)){
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }

    $yr = se($_GET, "number", "-1", false);
    if(!empty($yr) && $yr > -1){
        $query .= " AND year like :year";
        $params[':year'] = "%$yr%";
    }
    $text = se($_GET, "text", "", false);
    if(!empty($text)){
        $query .= " AND text like :text";
        $params[':text'] = "%$text%";
    }

    $sort = se($_GET, "sort", "year", false);
    if (!in_array($sort, ["year", "text"])) {
        $sort = "year";
    }
    $order = se($_GET, "order", "desc", false);
    if (!in_array($order, ["asc", "desc"])) {
        $order = "desc";
    }

    $query .= " ORDER BY $sort $order";

    try{
        $limit = (int)se($_GET, "limit", "10", false);
    }catch(Exception $e){
        $limit = 10;
    }

    if($limit < 1 || $limit > 100){
        $limit = 10;
    }

    $query .= " LIMIT $limit";

}

$db = getDB(); //bns24 04/14/24
$stmt = $db->prepare($query);
$results = [];
try{
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if($r){
        $results = $r;
    }
}

catch(PDOException $e){
    error_log("Error fetching year " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

//$table = ["data"=>$results, "title" => "Your Year List", "ignored_columns" => ["id"], "view_url"=>get_url("user_view_year.php")];
//$table = ["data"=>$results, "title" => "Your Year List", "ignored_columns" => ["id", "text", "year", "type"], "view_url"=>get_url("user_view_year.php")];
?>

<div class = "container-fluid">
    <h3>Years</h3>
    <form method = "GET">
        <div class = "row mb-3" style = "align-items: flex-end;">
            <?php foreach($form as $k=>$v) : ?>
                <div class = "col">
                    <?php render_input($v);?>
                </div>
            <?php endforeach;?>
        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href = "?clear" class = "btn btn-secondary">Clear</a>
    <div class = "row row-cols-sm-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach($results as $yr):?>  
            <?php $testarray = []; ?>
            <?php $testarray[0] = $yr?>
            <?php $table = ["data"=>$testarray, "ignored_columns" => ["id", "text", "year", "type"], "view_url"=>get_url("user_view_year.php"), "favorite_url"=>get_url("favorite_years.php")];?>
            <div class = "col">
                <?php render_card($yr, $table, false); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>