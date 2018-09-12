<?php
/**
 *     CCHits. Where you make the charts.
 *     Copyright (C) 2018  CCHits.net
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

use CCHits\Actions\TracksActions;
use CCHits\Providers\TracksProvider;
use Slim\App;

require "../vendor/autoload.php";

$settings = include("settings.inc.php");

$app = new App($settings);

$container = $app->getContainer();

$medoo_config = include("db.inc.php");
$container["db"] = new Medoo\Medoo($medoo_config);

$container[TracksActions::class] = function ($c) {
    /** @var \Medoo\Medoo $medoo */
    $medoo = $c["db"];
    $provider = new TracksProvider($medoo);
    return new TracksActions($provider);
};

$app->get("/tracks", TracksActions::class . ":enumerate");

/** @noinspection PhpUnhandledExceptionInspection */
$app->run();
