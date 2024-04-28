<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
//attempt to apply
if (isset($_POST["users"]) && isset($_POST["years"])) {
    $user_ids = $_POST["users"]; //se() doesn't like arrays so we'll just do this
    $year_ids = $_POST["years"]; //se() doesn't like arrays so we'll just do this
    if (empty($user_ids) || empty($year_ids)) {
        flash("Both users and roles need to be selected", "warning");
    } else {
        //for sake of simplicity, this will be a tad inefficient
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO UserFavorites (user_id, year_id, is_active) VALUES (:uid, :yid, 1) 
        ON DUPLICATE KEY UPDATE is_active = !is_active
        ");
        foreach ($user_ids as $uid) {
            foreach ($year_ids as $yid) {
                try {
                    $stmt->execute([":uid" => $uid, ":yid" => $yid]);
                    flash("Updated role", "success");
                } catch (PDOException $e) {
                    flash(var_export($e->errorInfo, true), "danger");
                }
            }
        }

        //checks for duplicates then deletes them
        $query = "DELETE FROM `UserFavorites` WHERE is_active = 0";
        try{
            $stmt = $db->prepare($query);
            $stmt->execute();
        }catch(Exception $e){
            error_log("Error deleting stock $id" . var_export($e, true));
            flash("Error deleting record", "danger");
        }
    }
}

//get active roles
$active_years = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, year, text, type FROM Numbers LIMIT 100");
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $active_years = $results;
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

//search for user by username
/*
$users = [];
$username = "";
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Users.id, username,
        (SELECT GROUP_CONCAT(' ID: [', Numbers.id, '] ', year, ' ') from 
        UserFavorites uf JOIN Numbers on uf.year_id = Numbers.id WHERE uf.user_id = Users.id) as years
        from Users WHERE username like :username");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);   
            if ($results) {
                $users = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}
*/

$users = [];
$username = "";
$year = "";
//bns24 04/27/24
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    $year = se($_POST, "year", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Users.id, username,
        (SELECT GROUP_CONCAT(' ID: [', Numbers.id, '] ', year, ' ') from 
        UserFavorites uf JOIN Numbers on uf.year_id = Numbers.id WHERE uf.user_id = Users.id AND Numbers.year like :year LIMIT 25) as years
        from Users WHERE username like :username LIMIT 25");
        try {
            $stmt->execute([":username" => "%$username%", ":year" => "%$year%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);   
            if ($results) {
                $users = $results;
            }
            foreach($users as $key => &$testarray){
                foreach($testarray as $k=>$v){
                    if($k === "years" && is_null($v)){
                        unset($users[$key]);
                    }
                }
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }elseif (!empty($year)){
        $db = getDB();
        $stmt = $db->prepare("SELECT Users.id, username,
        (SELECT GROUP_CONCAT(' ID: [', Numbers.id, '] ', year, ' ') from 
        UserFavorites uf JOIN Numbers on uf.year_id = Numbers.id WHERE uf.user_id = Users.id AND Numbers.year like :year LIMIT 25) as years
        from Users LIMIT 25");
        try {
            $stmt->execute([":year" => "%$year%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);   
            if ($results) {
                $users = $results;
            }
            foreach($users as $key => &$testarray){
                foreach($testarray as $k=>$v){
                    if($k === "years" && is_null($v)){
                        unset($users[$key]);
                    }
                }
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username and Year must not both be empty", "warning");
    }
}

?>
<div class="container-fluid">
    <h1>Assign Years</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]);/*lazy value to check if form submitted, not ideal*/ ?>
        <?php render_input(["type" => "search", "name" => "year", "placeholder" => "Year Search", "value" => $year]);/*lazy value to check if form submitted, not ideal*/ ?>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>
    <form method="POST">
        <?php if (isset($username) && !empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>
        <?php if (isset($year) && !empty($year)) : ?>
            <input type="hidden" name="year" value="<?php se($year, false); ?>" />
        <?php endif; ?>
        <table class="table">
            <thead>
                <th>Users</th>
                <th>Years to Assign</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="table">
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <label for="user_<?php se($user, 'id'); ?>"><?php se($user, "username"); ?></label>
                                        <input id="user_<?php se($user, 'id'); ?>" type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>" />
                                    </td>
                                    <td><?php se($user, "years", "No Years"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                    <td>
                        <?php foreach ($active_years as $year) : ?>
                            <div>
                                <label for="year_<?php se($year, 'id'); ?>"><?php se("ID: [") . se($year, "id") . se("] ") . se($year, "year"); ?></label>
                                <input id="year_<?php se($year, 'id'); ?>" type="checkbox" name="years[]" value="<?php se($year, 'id'); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php render_button(["text" => "Toggle Roles", "type" => "submit", "color" => "secondary"]); ?>
    </form>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>