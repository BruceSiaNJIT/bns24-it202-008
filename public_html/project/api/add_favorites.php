<?php
//no nav here because this is a temp file, not a user-facing file
require(__DIR__ . "/../../../lib/functions.php");
session_start();
if(isset($_GET["year_id"]) && is_logged_in()){
    $db = getDB();
    $query = "INSERT INTO `UserFavorites` (user_id, year_id) VALUES (:user_id, :year_id)";
    try{
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id"=>get_user_id(), ":year_id"=>$_GET["year_id"]]);
        flash("Added to Favorites", "success");
        redirect("my_favorites.php");
    }catch(PDOException $e){
        //probably shouldn't happen cause many to many
        if($e->errorInfo[1] == 1062){
            flash("You already have favorited this year.", "danger");
        }else{
            flash("Unhandled error occurred", "danger");
        }
        error_log("Error handling year: " . var_export($e, true));
    }
}

redirect("user_list_years.php");