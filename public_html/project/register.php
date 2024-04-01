<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<div class="container-fluid">
    <form onsubmit="return validate(this)" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" required class="form-control" />
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" required maxlength="30" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="pw" class="form-label">Password</label>
            <input type="password" id="pw" name="password" required minlength="8" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="confirm" class="form-label">Confirm</label>
            <input type="password" name="confirm" required minlength="8" class="form-control" />
        </div>
        <input type="submit" value="Register" class="btn btn-primary" />
    </form>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success
        var email = document.getElementsByName('email')[0].value;
        var password = document.getElementsByName('password')[0].value;
        var confirm = document.getElementsByName('confirm')[0].value;
        var username = document.getElementsByName('username')[0].value;

        if(email == ""){
            flash("[JS] Email cannot be empty.", "warning");
            return false;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            flash("[JS] Invalid email format", "warning");
            return false;
        }

        if(!/^[a-z0-9_-]{3,16}$/.test(username)){
            flash("[JS] Username must only contain 3-16 characters a-z, 0-9, _, or -", "warning");
            return false;
        }

        if(password == ""){
            flash("[JS] Password cannot be empty.", "warning");
            return false;
        }

        if(confirm == ""){
            flash("[JS] Confirm password cannot be empty.", "warning");
            return false;
        }

        if(password.length < 8){
            flash("[JS] Password must be atleast 8 characters.", "warning");
            return false;
        }

        if(confirm !== password){
            flash("[JS] Password and Confirm Password must match.", "warning");
            return false;
        }



        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must only contain 3-16 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
