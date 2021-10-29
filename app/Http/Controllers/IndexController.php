<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $existing_datasets = DB::connection('bayarea')->table("uploaded_datasets")
          ->select(DB::raw('dataset_name, count(id)'))
          ->groupBy('dataset_name')
          ->orderBy('dataset_name')
          ->get();
        return view("map", [
          "existing_datasets" => $existing_datasets
        ]);
    }
}
