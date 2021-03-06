<?php

namespace App\Http\Controllers;

use App\PinnedWebApp;
use App\WebApp;
use App\WebAppDependency;
use App\WebAppDeploymentLocation;
use App\WebAppHasWebAppDependency;
use App\WebAppVersion;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;

/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 4/13/2018
 * Time: 11:05 PM
 */
class SyncController extends Controller
{
    public function index()
    {
        $items_per_batch = 1000;
        $last_sync_ts = Input::get('last_sync_ts', 0);
        $batch_number = Input::get('batch_number', 0);
        $tables_to_sync = ['web_app_dependencies', 'web_app_deployment_locations', 'web_app_has_web_app_dependencies', 'web_app_versions', 'web_apps'];
        $records = [];
        foreach ($tables_to_sync as $table_to_sync) {
            $records[$table_to_sync] = \DB::table($table_to_sync)->where(\DB::raw('UNIX_TIMESTAMP(updated_at)'), '>', $last_sync_ts / 1000)
                ->skip($batch_number * $items_per_batch)
                ->take($items_per_batch)
                ->get();
        }
        $records['$items_per_batch'] = $items_per_batch;
        return $records;
    }
}