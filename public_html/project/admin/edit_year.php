<?php
//note we need to go up 1 more directory
//bns24 04/14/24
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php
$id = se($_GET, "id", -1, false);
//handle year fetch
if(isset($_POST["year"])){
    foreach($_POST as $k => $v){
        if(!in_array($k, ["number", "text", "type"])){
            unset($_POST[$k]);
        }
        $result = $_POST;
    }
    $db = getDB();
    $query = "UPDATE `Numbers` SET ";
    
    $params = [];

    foreach($result as $k => $v){
        //might need to do something like if $k is not equal to found and number
        if($k == "text" || $k == "number" || $k == "type"){
            if($k == "number"){
                $k = "year";
            }
            //array_push($params, [":$k"=>$v]);
            if($params){
                $query .= ",";
            }
            $query .= "$k=:$k";
            $params[":$k"] = $v;
        }
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: ". var_export($params, true));
    try{
        //bns24 04/14/24
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated record", "success");
    }catch(PDOException $e){
        error_log("Something went wrong.");
    }
}
$yr = [];
if($id > -1){
    //fetch
    $db = getDB();
    $query = "SELECT id, text, year, type FROM `Numbers` WHERE id = :id";
    try{
        $stmt = $db->prepare($query);
        $stmt->execute([":id"=>$id]);
        $r = $stmt->fetch();
        if($r){
            $yr = $r;
        }
    }catch(PDOException $e){
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record.", "danger");
    }
}else{
    flash("Invalid id passed", "danger");
    redirect("admin/list_years.php");
}

if($yr){
    //bns24 04/14/24
    $form = [
        ["type" => "number", "name" => "number", "placeholder" => "Year Number", "label" => "Year Number", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "text", "placeholder" => "Year Info", "label" => "Year Info", "rules" => ["required" => "required"]],
        ["type" => "select", "name" => "type", "label" => "Type", "options" => ["year" => "year"],"rules" => ["required" => "required"]]
    ];
    $keys = array_keys($yr);

    foreach($form as $k=>$v){
        if($v["name"] === "number"){
            $form[$k]["name"] = "year";
            $form[$k]["value"] = $yr["year"];
        }
        if(in_array($v["name"], $keys)){
            $form[$k]["value"] = $yr[$v["name"]];
        }
    }
}

?>
<div class = "container-fluid">
    <h3>Edit Year</h3>
    <div>
        <a href = "<?php echo get_url("admin/list_years.php");?>" class = "btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach($form as $k=>$v){
            render_input($v);
        }?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Update"]); ?>
    </form>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>