<?php 

require __DIR__."/includes/bundle.php";

?>

<html>
    <head>
        <title>Super Arrows</title>
    </head>
    <body>
        <form method="POST" action="generateResults.php">
            <?php 

                $pf = new PredictionFactory($db);

                $matches = unserialize($_POST['matches']);
                $predictionObjs = $pf->fromPostArrays(
                    $_POST['player1score'],
                    $_POST['player2score'],
                    $matches
                );

                //var_dump($predictionObjs);

                foreach($predictionObjs as $p) {
                    $pf->save($p);
                }

                echo "<h2>Predictions Submitted, Good Luck!</h2>";
                echo "<input type='hidden' name='predictions' value='$dataString'/>";
                // echo "<input type='submit' value='View Results'/>";
            ?>
        </form>
    </body>
</html>