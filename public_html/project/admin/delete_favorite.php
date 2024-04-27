<?php
//bns24 04/14/24
session_start();
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

$id = se($_GET, "id", -1, false);
$user_id = se($_GET, "user_id", -1, false);
var_export($id);
var_export($user_id);
if($id < 1){
    flash("Invalid id passed to delete", "danger");
    redirect("admin/list_years.php");
}

$db = getDB();
$query = "DELETE FROM `UserFavorites` WHERE year_id = :id AND user_id = :user_id";
try{
    $stmt = $db->prepare($query);
    $stmt->execute([":id"=>$id, ":user_id"=>$user_id]);
    flash("Unfavorited Year Successfully", "success");
}catch(Exception $e){
    error_log("Error deleting stock $id" . var_export($e, true));
    flash("Error deleting record", "danger");
}
redirect("my_favorites.php");