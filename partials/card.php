<?php 
if(!isset($data)){
    error_log("Using Card partial without data");
    flash("Dev Alert: Using Card partial without data", "danger");
}
?>

<?php if(isset($data)):?>
    <div class="card mx-auto" style="width: 18rem;">
        <?php if(isset($data["username"])):?>
            <div class = "card-header">
                Favorited By: <a href="<?php echo get_url("profile.php?id=" . $data["user_id"]); ?>"><?php se($data, "username", "N/A"); ?></a>
            </div>
        <?php endif;?>
        <div class="card-body">
            <h5 class="card-title"><?php se($data, "year", "Unknown"); ?></h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">Year: <?php se($data, "year", "Unknown"); ?></li>
                    <li class="list-group-item">Text: <?php se($data, "text", "Unknown"); ?></li>
                    <li class="list-group-item">Type: <?php se($data, "type", "Unknown"); ?></li>
                    <?php if($includesCreate):?>
                        <li class="list-group-item">Created: <?php se($data, "created", "Unknown"); ?></li>
                        <li class="list-group-item">Modified: <?php se($data, "modified", "Unknown"); ?></li>
                    <?php endif;?>
                </ul>
            </div>
            <div class="card-body">
                <?php if (isset($data["id"]) && $tabledata != []) : ?>
                    <?php if(!isset($tabledata["adminyear"])) :?> 
                        <a class="btn btn-primary" href="<?php echo get_url("user_view_year.php?id=" . $data["id"]); ?>">View</a>
                    <?php endif; ?> 
                    <?php if(isset($tabledata["deleteyear"])) : ?>
                        <?php if (isset($data["user_id"])) : ?>
                            <a class="btn btn-danger" href="<?php echo get_url("admin/delete_favorite.php?id=" . $data["id"] . "&user_id=" . $data["user_id"]); ?>">Unfavorite</a>
                        <?php else: ?>
                            <a class="btn btn-danger" href="<?php echo get_url("admin/delete_year.php?id=" . $data["id"]); ?>">Delete</a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!isset($data["user_id"]) || $data["user_id"] === "N/A") : ?>
                    <?php
                    $id = isset($data["id"]) ? $data["id"] : (isset($_GET["id"]) ? $_GET["id"] : -1);
                    ?>
                    <?php if($tabledata != []) :?>
                        <a href = "<?php echo get_url('api/add_favorites.php?year_id=' . $data["id"]);?>" class = "card-link">Add To Favorites</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif;?>