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

namespace CCHits\Actions;


use CCHits\Model\Track;
use CCHits\Providers\TracksProvider;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

class TracksActions
{
    /** @var TracksProvider */
    private $provider;

    /**
     * Tracks constructor.
     * @param $provider TracksProvider
     */
    public function __construct($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function enumerate(Request $request, Response $response)
    {
        $params = $request->getQueryParams();

        $offset = $this->getIntQueryParameter($request, "offset", 0);
        $limit = $this->getIntQueryParameter($request, "limit", null, $query_limit);
        $fullcount = $this->getBooleanQueryParam($request, "fullcount", false);
        $order = $this->getStringQueryParam($request, "order");
        $titlesearch = $this->getStringQueryParam($request, "titlesearch");

        if (is_int($limit)) {
            $limit = intval(filter_var($limit, FILTER_SANITIZE_NUMBER_INT));
            if (($limit > 200) || ($limit < 1)) {
                return $this->fail($response, ResponseCodeEnum::TYPE, "The 'limit' parameter must be an integer >= 1 and <= 200");
            }
        } else {
            if ($query_limit === "all") {
                $limit = 200;
            } elseif (!is_null($query_limit)) {
                return $this->fail($response, ResponseCodeEnum::TYPE, "The 'limit' parameter must be an integer >= 1 and <= 200");
            } else {
                $limit = 20;
            }
        }

        $sort_order = SortOrderEnum::ASC;
        if (substr($order, -5) === "_desc") {
            $order = substr($order, 0, strlen($order) - 5);
            $sort_order = SortOrderEnum::DESC;
        }

        $order_columns = ["id" => "intTrackID"];

        if (!is_null($order) && !array_key_exists($order, $order_columns)) {
            return $this->fail($response, ResponseCodeEnum::TYPE, "Parameter 'order' accept only values in [" . join(", ", array_keys($order_columns)) . "]. '" . $order . "' given.");
        }

        $log = [];

        $where = [];

        if (!is_null($order)) {
            $where["ORDER"] = [$order_columns[$order] => ($sort_order == SortOrderEnum::ASC) ? "ASC" : "DESC"];
        }

        if (!is_null($titlesearch)) {
            $where["strTrackName[~]"] = $titlesearch;
        }

        if ($fullcount) {
            $results_fullcount = $this->provider->count($where, $log);
        }

        $where["LIMIT"] = [$offset, $limit];

        $rows = $this->provider->select($where, $log);

        $results = $this->sanitize_tracks($rows);

        $results_count = count($results);

        /** @var Uri $uri */
        $uri = $request->getUri();

        $params["offset"] = $offset + $limit;
        $str_params = [];
        foreach ($params as $key => $value) {
            $str_params[] = $key . "=" . $value;
        }
        $next = $uri->getBaseUrl() . "/" . $uri->getPath() . "?" . join("&", $str_params);

        $headers = [
            "status" => "success",
            "code" => 0,
            "error_message" => "",
            "warnings" => [],
            "next" => $next,
            "results_count" => $results_count,
        ];

        if ($fullcount) {
            /** @noinspection PhpUndefinedVariableInspection */
            $headers["results_fullcount"] = $results_fullcount;
        }

        $response = $response->withJson(["headers" => $headers, "results" => $results, "log" => $log], 200);

        return $response;
    }

    /**
     * @param $request Request
     * @param $key string
     * @param int|null $default
     * @param string|null $query_value
     * @return int|null
     */
    private function getIntQueryParameter($request, $key, $default = null, &$query_value = null)
    {
        return $this->getQueryParam($request, $key, FILTER_VALIDATE_INT, $default, $query_value);
    }

    /**
     * @param $request Request
     * @param $key string
     * @param $filter_type int
     * @param mixed $default
     * @param string|null $query_value
     * @return mixed
     */
    private function getQueryParam($request, $key, $filter_type, $default = null, &$query_value = null)
    {
        $query_value = $request->getQueryParam($key, null);
        if (is_null($query_value) || ($query_value === "")) return $default;
        if (!is_null($filter_type)) {
            $filter = filter_var($query_value, $filter_type, FILTER_NULL_ON_FAILURE);
            if ($filter === null) return $default;
            return $filter;
        } else
            return $query_value;
    }

    /**
     * @param $request Request
     * @param $key string
     * @param $default bool|null
     * @param string|null $query_value
     * @return bool|null
     */
    private function getBooleanQueryParam($request, $key, $default = null, &$query_value = null)
    {
        return $this->getQueryParam($request, $key, FILTER_VALIDATE_BOOLEAN, $default, $query_value);
    }

    private function getStringQueryParam($request, $key, $default = null, &$query_value = null)
    {
        return $this->getQueryParam($request, $key, null, $default, $query_value);
    }

    /**
     * @param Response $response
     * @param int $code
     * @param string $message
     * @return Response
     */
    private function fail($response, $code, $message)
    {
        $headers = [
            "status" => "failed",
            "code" => $code,
            "error_message" => $message,
            "warnings" => [],
            "results_count" => 0,
        ];

        $results = [];

        $response = $response->withJson(["headers" => $headers, "results" => $results], 200);

        return $response;
    }

    /**
     * @param $rows array
     * @return Track[]
     */
    private function sanitize_tracks($rows)
    {
        $tracks = [];

        foreach ($rows as $row) {
            $tracks[] = $this->sanitize_track($row);
        }

        return $tracks;
    }

    /**
     * @param $row array
     * @return Track
     */
    private function sanitize_track($row)
    {
        $track = new Track();

        $track->id = intval($row["intTrackID"]);
        $track->artist_id = intval($row["intArtistID"]);
        $track->title = $this->unwrap($row["strTrackName"]);
        $track->title_sounds_like = $this->unwrap($row["strTrackNameSounds"]);
        $track->url = $this->unwrap($row["strTrackUrl"]);
        $track->license = $row["enumTrackLicense"];
        $track->nsfw = (intval($row["isNSFW"]) == 0) ? false : true;
        $track->source = $row["fileSource"];
        $track->str_duration = $row["timeLength"];
        $track->duration = $this->parse_str_duration($track->str_duration);
        $track->date_added_utc = $row["dtsAdded"];
        $track->ts_added_utc = strtotime($row["dtsAdded"]);
        $track->date_daily_show_utc = $this->format_date_ordinal($row["datDailyShow"]);
        $track->ts_daily_show_utc = strtotime("midnight", strtotime($track->date_daily_show_utc));
        $track->chart_position = intval($row["intChartPlace"]);

        return $track;
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function unwrap($value)
    {
        $first_char = substr($value, 0, 1);
        $last_char = substr($value, -1);
        if (($first_char == "{") && ($last_char == "}")) {
            $json_value = json_decode($value);
            try {
                $value = $json_value->preferred;
            } catch (\Exception $e) {
                $value = null;
            }
        } elseif (($first_char === "[") && ($last_char === "]")) {
            $json_value = json_decode($value);
            $value = $json_value[0];
        }
        $value = html_entity_decode($value, FILTER_SANITIZE_STRING);
        $value = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
        }, $value);

        return $value;
    }

    /**
     * @param string $str_duration
     * @return int|null
     */
    private function parse_str_duration($str_duration)
    {
        $matched = preg_match("/^([0-9]+):([0-9]+):([0-9]+)/", $str_duration, $matches);

        if ($matched === false) return null;

        $duration = intval($matches[3]) + 60 * intval($matches[2]) + 3600 * intval($matches[1]);

        return $duration;
    }

    /**
     * @param string $date_ordinal
     * @return string|null
     */
    private function format_date_ordinal($date_ordinal)
    {
        $matched = preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})/", $date_ordinal, $matches);

        if ($matched === false) return null;

        return $matches[1] . "-" . $matches[2] . "-" . $matches[3] . " 00:00:00";
    }
}