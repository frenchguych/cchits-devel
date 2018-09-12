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

namespace CCHits\Providers;


use Medoo\Medoo;

class TracksProvider
{
    const TABLE = "tracks";

    const COLUMNS = [
        "intTrackID",
        "intArtistID",
        "strTrackName",
        "strTrackNameSounds",
        "strTrackUrl",
        "enumTrackLicense",
        "isNSFW",
        "fileSource",
        "timeLength",
        "dtsAdded",
        "datDailyShow",
        "intChartPlace"
    ];

    /** @var Medoo */
    private $medoo;

    /**
     * TracksProvider constructor.
     * @param $medoo Medoo
     */
    public function __construct($medoo)
    {
        $this->medoo = $medoo;
    }

    /**
     * @param $log array|null
     */
    private function log(&$log)
    {
        if (!is_null($log) && is_array($log)) {
            $log[] = $this->medoo->log();
            $error = $this->medoo->error();
            if (!is_null($error[2])) {
                $log[] = $error;
            }
        }
    }

    /**
     * @param $where array
     * @param $log array|null
     * @return array|bool
     */
    public function select($where, &$log = null)
    {
        $rows = $this->medoo->select(
            self::TABLE,
            self::COLUMNS,
            $where
        );

        $this->log($log);

        return $rows;
    }

    /**
     * @param $where array
     * @param $log array|null
     * @return int
     */
    public function count($where, &$log = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $count = $this->medoo->count(
            self::TABLE,
            $where
        );

        $this->log($log);

        return is_bool($count) ? 0 : $count;
    }
}