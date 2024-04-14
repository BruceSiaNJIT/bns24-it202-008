<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
//handle year fetch
if(isset($_POST["action"])){
    $action = $_POST["action"];
    $year = se($_POST, "number", "", false);
    if($year){
        if($action === "fetch"){
            $result = fetch_quote($year);
        }else if($action === "create"){
            foreach($_POST as $k => $v){
                if(!in_array($k, ["number", "text", "type"])){
                    unset($_POST[$k]);
                }
                $result = $_POST;
            }
        }
    }else{
        flash("You must provide a year", "warning");
    }
    $db = getDB();
    $query = "INSERT INTO `Numbers` ";
    $columns = [];
    $params = [];

    foreach($result as $k => $v){
        //might need to do something like if $k is not equal to found and number
        if($k == "text" || $k == "number" || $k == "type"){
            if($k == "number"){
                $k = "year";
            }
            array_push($columns, "`$k`");
            //array_push($params, [":$k"=>$v]);
            $params[":$k"] = $v;
        }
    }

    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",", array_keys($params)) . ")";
    var_export($query);
    error_log("Query: " . $query);
    error_log("Params: ". var_export($params, true));
    try{
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
    }catch(PDOException $e){
        error_log("Something went wrong.");
    }
}

?>
<div class = "container-fluid">
    <h3>Create or Fetch Year</h3>
    <ul class = "nav nav-tabs">
        <li class = "nav-item bg-info">
            <a class = "nav-link" href = "#" onclick = "switchTab('create')">Fetch</a>
        </li>
        <li class = "nav-item bg-info">
            <a class = "nav-link" href = "#" onclick = "switchTab('fetch')">Create</a>
        </li>
    </ul>

    <div id = "fetch" class = "tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "number", "placeholder" => "Year Number", "rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]);?>
            <?php render_button(["text" => "Search", "type" => "submit", ]); ?>
        </form>
    </div>
    <div id = "create" style = "display:none;" class = "tab-target">
        <form method="POST">
            <?php render_input(["type" => "number", "name" => "number", "placeholder" => "Year Number", "label" => "Year Number", "rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_input(["type" => "text", "name" => "text", "placeholder" => "Year Info", "label" => "Year Info", "rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_input(["type" => "text", "name" => "type", "placeholder" => "Type", "label" => "Type", "rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]);?>
            <?php render_button(["text" => "Search", "type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
</div>

<script>
function switchTab(tab){
    let target = document.getElementById(tab);
    if(target){
        let eles = document.getElementsByClassName("tab-target");
        for(let ele of eles){
            ele.style.display = (ele.id === tab) ? "none":"block"; 
        }
    }
}
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>