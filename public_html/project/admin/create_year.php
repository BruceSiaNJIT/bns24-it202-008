<?php
//note we need to go up 1 more directory bns24 04/14/24
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php

//handle year fetch bns24 04/14/24
if(isset($_POST["action"])){
    $action = $_POST["action"];
    $year = se($_POST, "number", "", false);
    $quote = [];
    if($year){
        if($action === "fetch"){
            $result = fetch_quote($year);
            if($result){
                $quote = $result;
                $quote["is_api"] = 1;
            }
            foreach($quote as $k => &$v){
                if(!in_array($k, ["number", "text", "type", "year"])){
                    unset($quote[$k]);
                }
                if($k === "number"){
                    $quote["year"] = $quote[$k];
                    unset($quote[$k]);
                }
            }
        }else if($action === "create"){
            foreach($_POST as $k => &$v){
                if(!in_array($k, ["number", "text", "type", "year"])){
                    unset($_POST[$k]);
                }
                if($k === "number"){
                    $_POST["year"] = $_POST[$k];
                    unset($_POST[$k]);
                }
                $quote = $_POST;
            }
        }
    }else{
        flash("You must provide a year", "warning");
    }

    try{
        $result = insert("Numbers", $quote);
        if(!$result){
            flash("Unhandled error", "warning");
        }else{
            flash("Inserted Record", "success");
        }
    }catch(InvalidArgumentException $e1){
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    }catch(PDOException $e2){
        if($e2->errorInfo[1] == 1062){
            flash("The info statement has already been used previously, please try another.", "warning");
        }else{
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    }catch(Exception $e3){
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }

    /*$db = getDB();
    $query = "INSERT INTO `Numbers` ";
    $columns = [];
    $params = [];

    foreach($result as $k => $v){
        //might need to do something like if $k is not equal to found and number
        if($k == "text" || $k == "number" || $k == "type" || $k == "is_api"){
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
    error_log("Query: " . $query);
    error_log("Params: ". var_export($params, true));
    //bns24 04/14/24
    try{
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
    }catch(PDOException $e){
        if($e->errorInfo[1] == 1062){
            flash("Another year has the same description, please use another description.", "warning");
        }else{
            error_log("Something went wrong.");
            flash("An error occurred.", "danger");
        }
    }*/
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
            <!-- bns24 04/14/24 -->
            <?php render_input(["type" => "number", "name" => "number", "placeholder" => "Year Number", "label" => "Year Number", "rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_input(["type" => "text", "name" => "text", "placeholder" => "Year Info", "label" => "Year Info", "rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_input(["type" => "select", "name" => "type", "label" => "Type", "options" => ["year" => "year"],"rules" => ["required" => "required"]]);/*lazy value to check if form submitted, not ideal*/ ?>
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