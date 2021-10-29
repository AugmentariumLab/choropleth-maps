<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $dataset_file = $request->file("dataset-file");
        $dataset_name = $request->input("dataset-name");
        $latitude_column = $request->input("latitude-column");
        $longitude_column = $request->input("longitude-column");
        $zipcode_column = $request->input("zipcode-column");
        $mandatory_fields = array(
          "dataset_file" => $dataset_file,
          "dataset_name" => $dataset_name,
          "latitude_column" => $latitude_column,
          "longitude_column" => $longitude_column,
          "zipcode_column" => $zipcode_column
        );
        foreach ($mandatory_fields as $key => &$value) {
            if (is_null($value)) {
                return response("Missing $key", 400);
            }
        }


        $latitude_column_num = false;
        $longitude_column_num = false;
        $zipcode_column_num = false;
        $new_dataset_rows = array();
        if (($handle = fopen($dataset_file->path(), "r")) !== false) {
            if (($data = fgetcsv($handle, 0, ",")) !== false) {
                if (mb_detect_encoding($data[0]) === 'UTF-8') {
                    // delete BOM
                    $data[0] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $data[0]);
                }
                $latitude_column_num = array_search($latitude_column, $data);
                $longitude_column_num = array_search($longitude_column, $data);
                $zipcode_column_num = array_search($zipcode_column, $data);
            }
            $mandatory_fields = array(
              "latitude_column_num" => $latitude_column_num,
              "longitude_column_num" => $longitude_column_num,
              "zipcode_column_num" => $zipcode_column_num,
            );
            foreach ($mandatory_fields as $key => &$value) {
                if ($value === false) {
                    fclose($handle);
                    return response("Missing $key", 400);
                }
            }
            while (($data = fgetcsv($handle, 0, ",")) !== false) {
                // if (!is_numeric($data[$latitude_column_num])) {
                //     fclose($handle);
                //     return response("Invalid latitude found: $data[$latitude_column_num]", 400);
                // }
                // if (!is_numeric($data[$latitude_column_num])) {
                //     fclose($handle);
                //     return response("Invalid longitude found: $data[$longitude_column_num]", 400);
                // }
                // if (!is_numeric($data[$zipcode_column_num])) {
                //     fclose($handle);
                //     return response("Invalid zipcode found: $data[$zipcode_column_num]", 400);
                // }
                if (is_numeric($data[$latitude_column_num]) &&
                    is_numeric($data[$latitude_column_num]) &&
                    is_numeric($data[$zipcode_column_num])) {
                  $new_row = array(
                    "dataset_name" => $dataset_name,
                    "lat" => floatval($data[$latitude_column_num]),
                    "lon" => floatval($data[$longitude_column_num]),
                    "zip_code" => intval($data[$zipcode_column_num])
                  );
                  array_push($new_dataset_rows, $new_row);
                }

            }
            fclose($handle);
        }
        $new_dataset_rows_chunked = array_chunk($new_dataset_rows,10000,true);

        foreach ($new_dataset_rows_chunked as $new_dataset_rows_sub)
        {
            DB::connection('bayarea')->table("uploaded_datasets")
              ->insert($new_dataset_rows_sub);
        }
        // DB::connection('bayarea')->table("uploaded_datasets")
        //   ->insert($new_dataset_rows);
        return redirect("/datasets");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request)
    {
        $dataset_name = $request->input("dataset-name");
        DB::connection('bayarea')->table("uploaded_datasets")
          ->where("dataset_name", $dataset_name)
          ->delete();
        return redirect("/datasets");
    }
}
