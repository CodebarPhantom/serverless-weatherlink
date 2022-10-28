<?php

namespace App\Http\Controllers;

use App\Models\WeatherHistoryReport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class WeatherHistoryReportController extends Controller
{
    public function index()
    {
        return view('weather-history.report');
    }

    public function data()
    {

        $reports = WeatherHistoryReport::orderBy('created_at','desc');

        return DataTables::of($reports)
        ->editColumn('path_s3', function ($report) {
            $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
            return '<strong><a href="'.$url.$report->path_s3.'" style="text-decoration: none;">Download</a></strong>';
        })
        ->rawColumns(['path_s3'])
        ->make(true);
    }
}
