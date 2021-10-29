<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BayAreaController extends Controller
{

    public function query_by_zip(Request $request) {
      $zipcodes = $request->input("zipcodes");
      if ($zipcodes == null) {
        return "Invalid Query, missing zipcodes";
      }
      $zipcodes = explode(",", $zipcodes);
      if ($zipcodes == null) {
        return "Invalid Query";
      }
      $sources = $request->input("sources");
      $targets = $request->input("targets");
      if ($sources == null || $sources == "" || $targets == null || $targets == "") {
        return "{}";
      }

      $sources_query = $this->build_locations_query($sources);
      $targets_query = $this->build_locations_query($targets);
      // "SELECT avg(t1.dist) FROM (SELECT DISTINCT ON (id1) x.id AS id1, y.id AS id2, dist(x.code, y.code) AS dist, capacity, x.zip_code
      //    FROM restaurants x, parking y
      //    WHERE x.id != y.id AND x.lat <= y.lat + 0.5 / 110 AND x.lat >= y.lat - 0.5 / 110
      //    AND x.lon <= y.lon + 0.5 / 110 AND x.lon >= y.lon - 0.5 / 110
      //  AND x.zip_code = '{$zipcode}'
      //  ORDER BY x.id, dist) t1"
      $distance_count = DB::connection('bayarea')
              ->table(DB::raw("({$sources_query->toSql()}) x, ({$targets_query->toSql()}) y"))
              ->mergeBindings($sources_query)
              ->mergeBindings($targets_query)
              ->select("x.id")
              ->whereRaw("x.id != y.id")
              ->whereRaw("x.lat <= y.lat + 2.0 / 110")
              ->whereRaw("x.lat >= y.lat - 2.0 / 110")
              ->whereRaw("x.lon <= y.lon + 2.0 / 110")
              ->whereRaw("x.lon >= y.lon - 2.0 / 110")
              ->whereIn("x.zip_code", $zipcodes)
              ->count();
      $max_distances = config('app.max_distance_count');
      $subquery = DB::connection('bayarea')
              ->table(DB::raw("({$sources_query->toSql()}) x, ({$targets_query->toSql()}) y"))
              ->mergeBindings($sources_query)
              ->mergeBindings($targets_query)
              ->select("x.id as id1", "y.id as id2", DB::raw("dist(x.lat, x.lon, y.lat, y.lon) as dist"),
                       "x.zip_code")
              ->distinct("id1")
              ->whereRaw("x.id != y.id")
              ->whereRaw("x.lat <= y.lat + 2.0 / 110")
              ->whereRaw("x.lat >= y.lat - 2.0 / 110")
              ->whereRaw("x.lon <= y.lon + 2.0 / 110")
              ->whereRaw("x.lon >= y.lon - 2.0 / 110")
              ->whereIn("x.zip_code", $zipcodes)
              ->orderBy("x.id", "asc")
              ->orderBy("dist", "asc")
              ->limit($max_distances);
      $distance_count = min($distance_count, $max_distances);
      $mainquery = DB::connection('bayarea')->table(DB::raw("({$subquery->toSql()}) as t1"))
      ->mergeBindings($subquery)
      ->select("t1.zip_code", DB::raw("avg(t1.dist)"))
      ->groupBy('t1.zip_code');
      if (config("app.debug") && $request->has("dumpsql")) {
        $query_str = Str::replaceArray('?', $mainquery->getBindings(), $mainquery->toSql());
        return $query_str;
      }
      $query_results = $mainquery->get();
      return array(
        "distance_count"=>$distance_count,
        "query_results"=>$query_results
      );
    }

    private function build_locations_query($locations) {
      return DB::connection('bayarea')->table("uploaded_datasets")
        ->select()
        ->where("dataset_name", $locations);

    }

    public function benchmark(Request $request) {
      $zipcodes = $request->input("zipcodes");
      if ($zipcodes == null) {
        return "Invalid Query, missing zipcodes";
      }
      $zipcodes = explode(",", $zipcodes);
      if ($zipcodes == null) {
        return "Invalid Query";
      }
      $sources = $request->input("sources");
      $targets = $request->input("targets");

      $sources_query = $this->build_locations_query($sources);
      $targets_query = $this->build_locations_query($targets);
      $num_sources = $sources_query->count();
      $num_targets = $targets_query->count();
      $distance_count = DB::connection('bayarea')
              ->table(DB::raw("({$sources_query->toSql()}) x, ({$targets_query->toSql()}) y"))
              ->mergeBindings($sources_query)
              ->mergeBindings($targets_query)
              ->select("x.id")
              ->whereRaw("x.id != y.id")
              ->whereRaw("x.lat <= y.lat + 2.0 / 110")
              ->whereRaw("x.lat >= y.lat - 2.0 / 110")
              ->whereRaw("x.lon <= y.lon + 2.0 / 110")
              ->whereRaw("x.lon >= y.lon - 2.0 / 110")
              ->whereIn("x.zip_code", $zipcodes)
              ->limit(10000)
              ->count();
      $subquery = DB::connection('bayarea')
              ->table(DB::raw("({$sources_query->toSql()}) x, ({$targets_query->toSql()}) y"))
              ->mergeBindings($sources_query)
              ->mergeBindings($targets_query)
              ->select("x.id as id1", "y.id as id2", DB::raw("dist(x.lat, x.lon, y.lat, y.lon) as dist"),
                       "x.zip_code")
              ->distinct("id1")
              ->whereRaw("x.id != y.id")
              ->whereRaw("x.lat <= y.lat + 2.0 / 110")
              ->whereRaw("x.lat >= y.lat - 2.0 / 110")
              ->whereRaw("x.lon <= y.lon + 2.0 / 110")
              ->whereRaw("x.lon >= y.lon - 2.0 / 110")
              ->whereIn("x.zip_code", $zipcodes)
              ->orderBy("x.id", "asc")
              ->orderBy("dist", "asc")
              ->limit(10000);
      $mainquery = DB::connection('bayarea')->table(DB::raw("({$subquery->toSql()}) as t1"))
      ->mergeBindings($subquery)
      ->select("t1.zip_code", DB::raw("avg(t1.dist)"))
      ->groupBy('t1.zip_code');
      if (config("app.debug") && $request->has("dumpsql")) {
        $query_str = Str::replaceArray('?', $mainquery->getBindings(), $mainquery->toSql());
        return $query_str;
      }
      $start_time = microtime(true);
      $query_results = $mainquery->get();
      $end_time = microtime(true);
      $main_query_time = $end_time - $start_time;
      return array(
        "distance_count"=>$distance_count,
        "main_query_time"=>$main_query_time,
        "num_sources"=>$num_sources,
        "num_targets"=>$num_targets
      );
    }
}
