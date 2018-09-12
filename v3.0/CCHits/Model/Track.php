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

namespace CCHits\Model;


class Track
{
    /** @var int */
    public $id;

    /** @var int */
    public $artist_id;

    /** @var string */
    public $title;

    /** @var string */
    public $title_sounds_like;

    /** @var string */
    public $url;

    /** @var string */
    public $license;

    /** @var bool */
    public $nsfw;

    /** @var string */
    public $source;

    /** @var string */
    public $str_duration;

    /** @var int */
    public $duration;

    /** @var string */
    public $date_added_utc;

    /** @var int */
    public $ts_added_utc;

    /** @var string */
    public $date_daily_show_utc;

    /** @var int */
    public $ts_daily_show_utc;

    /** @var int */
    public $chart_position;
}