<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatasetManagerController extends Controller
{
  public function index()
  {
      $existing_datasets = DB::connection('bayarea')->table("uploaded_datasets")
        ->select(DB::raw('dataset_name, count(id)'))
        ->groupBy('dataset_name')
        ->get();
      return view("dataset_manager", [
        "existing_datasets" => $existing_datasets
      ]);
  }
}
