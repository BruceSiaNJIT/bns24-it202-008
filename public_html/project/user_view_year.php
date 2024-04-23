<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");
?>

<?php
$id = se($_GET, "id", -1, false);

$yr = [];
if ($id > -1) {
    //fetch bns24 04/14/24
    $db = getDB();
    $query = "SELECT id, year, text, type, created, modified FROM `Numbers` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $yr = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    redirect("admin/list_years.php");
}
foreach ($yr as $key => $value) {
    if (is_null($value)) {
        $yr[$key] = "N/A";
    }
}

$data = [];
$data[0] = $yr;

$table = [];

//TODO handle manual create stock
?>

<div class="container-fluid">
    <h3>Year: <?php se($yr, "year", "Unknown"); ?></h3>
    <div>
        <a href="<?php echo get_url("user_list_years.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <!-- bns24 04/24/24 -->
    <?php render_card($yr, $table); ?>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>