<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if(isset($_GET["year"])){
    $year = $_GET["year"];
    $result = get("https://numbersapi.p.rapidapi.com/$year/year", "NUMBER_API_KEY", ["fragment" => true, "json" => true]);
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
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
    try{
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record", "success");
    }catch(PDOException $e){
        error_log("Something went wrong.");
    }
}
?>


<div class="container-fluid">
    <h1>Numbers</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>YEAR</label>
            <input name = "year" />
            <input type = "submit" value = "Get Year Fact"/>
        </div>
    </form>
    <div class="row ">
        <?php if(isset($result)): ?>
            <pre>
            <?php //var_export($number)
            ?>
            </pre>
            <table>
                <thead>
                    <?php foreach ($result as $k => $v) : ?>
                        <td><?php se($k); ?></td>
                    <?php endforeach; ?>
                </thead>
                <tbody>
                    <tr>
                        <?php foreach ($result as $k => $v) : ?>
                            <td><?php se($v); ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");