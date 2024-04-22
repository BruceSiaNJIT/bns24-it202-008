<?php 
if(!isset($data)){
    error_log("Using Card partial without data");
    flash("Dev Alert: Using Card partial without data", "danger");
}
?>

<?php if(isset($data)):?>
    <div class="card mx-auto" style="width: 18rem;">
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
            <?php if(!($tabledata === [])):?>
                <?php render_table($tabledata); ?>
            <?php endif;?>
        </div>
    </div>
<?php endif;?>