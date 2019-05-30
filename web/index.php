<?php

require_once('WeatherForZip.php');

session_start();
$zip = $_GET['zip'] ?? $_SESSION['zip'];
if (!empty($zip)) {
    $_SESSION['zip'] = $zip;
}
$openWeatherMap = new WeatherForZip($zip);
?>

<html lang="en">

<head>
    <title>Weather Time</title>
</head>

<body>
    <h1>Weather</h1>
    <div>
        <form action="">
            <label for="zip">
                Zip
                <input name="zip" value="<?= $openWeatherMap->getZip() ?>">
            </label>
            <button type="submit">Go</button>
        </form>
    </div>
    <?php
    if ($openWeatherMap->hasErrors()) {
        $errors = $openWeatherMap->getErrors();
        ?>
        <div>
            Error<?= count($errors) > 1 ? 's' : '' ?> retrieving results:
            <ul>
                <?php
                foreach ($errors as $error) {
                    ?><li><?= $error ?></li><?php
                } ?>
            </ul>
        </div>
        <?php
    } else {
        ?><h2>Results for <?= $zip ?></h2><?php
        if ($openWeatherMap->hasLocationData()) {
            ?>
            <div>
                <h3>Location Data:</h3>
                <ul>
                    <?php
                    foreach ($openWeatherMap->getLocationData() as $field => $value) {
                        ?>
                        <li>
                            <strong><?= ucwords($field) ?>:</strong>
                            <?= $value ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }

        if ($openWeatherMap->hasGeneralWeatherData()) {
            ?>
            <div>
                <h3>General Weather Data:</h3>
                <ul>
                    <?php
                    foreach ($openWeatherMap->getGeneralWeatherData() as $field => $value) {
                        ?>
                        <li>
                            <strong><?= ucwords($field) ?>:</strong>
                            <?= $value ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }

        if ($openWeatherMap->hasWeatherReports()) {
            $weatherReports = $openWeatherMap->getWeatherReports();
            ?>
            <div>
                <h3>
                    Weather Report<?= count($weatherReports) > 1 ? 's' : '' ?>:
                </h3>
                <ul>
                    <?php
                    foreach ($weatherReports as $weatherReport) {
                        ?>
                        <li>
                            <strong><?= $weatherReport->main ?>:</strong>
                            <?= $weatherReport->description ?>
                            <img src="//openweathermap.org/img/w/<?= $weatherReport->icon ?>.png" />
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
    }
    ?>
</body>

</html>
