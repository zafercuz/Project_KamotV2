<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 180);

use Illuminate\Http\Request;
use DB;
use App\UserInfo;
use App\CheckInOut;
use App\Branch;
// use App\Helpers\DatabaseConnection;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Config;

class HomeController extends Controller
{
    public function index()
    {
        $branchModel = new Branch;
        $branchModel->setConnection('branch');
        $branch = $branchModel->get();

        return view('index', compact('branch'));
    }

    public function dataHRISId($logs, $collection, $from, $to)
    {
        $currentDate = $from;
        while ($currentDate <= $to) {
            $row = collect([]);
            $row->push([
                "id" => $logs[0]['userid'],
                "date" => $currentDate,
                "in" => "N/A",
                "out" => "N/A",
                "name" => $logs[0]['name'],
                "badgeNumber" => $logs[0]['Badgenumber'],
                ]);

            $queryDates = $logs->filter(function ($value, $key) use ($currentDate) {
                return explode(" ", $value->checktime)[0] == $currentDate;
            })->values();
            
            $ins = $queryDates->filter(function ($value, $key) {
                return strtoupper($value->checktype) == 'I';
            })->values();
            $outs = $queryDates->filter(function ($value, $key) {
                return strtoupper($value->checktype) == 'O';
            })->values();

            $firstIn = null;
            $lastOut = null;

            if (count($ins) > 0) {
                $firstIn = $ins[0]['checktime'];
                for ($counter = 1; $counter < count($ins); $counter++) {
                    $element = $ins[$counter];
                    if ($element['checktime'] < $firstIn) {
                        $firstIn = $element['checktime'];
                    }
                }
            }

            if (count($outs) > 0) {
                $lastOut = $outs[0]['checktime'];
                for ($counter = 1; $counter < count($outs); $counter++) {
                    $element = $outs[$counter];
                    if ($element['checktime'] > $lastOut) {
                        $lastOut = $element['checktime'];
                    }
                }
            }

            $changeIn = $row->replaceRecursive([0 => ['in' =>  $firstIn ? $firstIn : 'N/A']]);
            $row = $changeIn;

            $changeOut = $row->replaceRecursive([0 => ['out' => $lastOut ? $lastOut : 'N/A']]);
            $row = $changeOut;

            $collection->push($row);
            $currentDate = Carbon::parse($currentDate)->addDay()->format('Y-m-d');
        }
        return $collection;
    }

    public function dataBranchEmployeeName($logs, $collection, $from, $to)
    {
        $currentDate = $from; // Get start date
        $getUniqueIdName = collect([]);
        foreach ($logs as $key => $value) {
            if (!$getUniqueIdName->contains("id", $value->userid)) {
                $getUniqueIdName->push(["id" => $value->userid, "name" => $value->name, "badgeNumber" => $value->Badgenumber]);
            }
        }
        $idCounter = 0;
        while ($currentDate <= $to) {
            $row = collect([]);
            $row->push([
                "id" => $getUniqueIdName[$idCounter]['id'],
                "date" => $currentDate,
                "in" => "N/A",
                "out" => "N/A",
                "name" => $getUniqueIdName[$idCounter]['name'],
                "badgeNumber" => $getUniqueIdName[$idCounter]['badgeNumber'],
                ]);

            $queryDates = $logs->filter(function ($value, $key) use ($currentDate, $getUniqueIdName, $idCounter) {
                return explode(" ", $value->checktime)[0] == $currentDate && $value->userid == $getUniqueIdName[$idCounter]['id'];
            })->values();

            $ins = $queryDates->filter(function ($value, $key) {
                return strtoupper($value->checktype) == 'I';
            })->values();

            $outs = $queryDates->filter(function ($value, $key) {
                return strtoupper($value->checktype) == 'O';
            })->values();
            
            $firstIn = null;
            $lastOut = null;

            if (count($ins) > 0) {
                $firstIn = $ins[0]['checktime'];
                for ($counter = 1; $counter < count($ins); $counter++) {
                    $element = $ins[$counter];
                    if ($element['checktime'] < $firstIn) {
                        $firstIn = $element['checktime'];
                    }
                }
            }

            if (count($outs) > 0) {
                $lastOut = $outs[0]['checktime'];
                for ($counter = 1; $counter < count($outs); $counter++) {
                    $element = $outs[$counter];
                    if ($element['checktime'] > $lastOut) {
                        $lastOut = $element['checktime'];
                    }
                }
            }

            $changeIn = $row->replaceRecursive([0 => ['in' =>  $firstIn ? $firstIn : 'N/A']]);
            $row = $changeIn;

            $changeOut = $row->replaceRecursive([0 => ['out' => $lastOut ? $lastOut : 'N/A']]);
            $row = $changeOut;
            

            $collection->push($row);
            
            if ($currentDate == $to && !($idCounter+1 == count($getUniqueIdName))) {
                $idCounter += 1;
                $currentDate = $from;
            } else {
                $currentDate = Carbon::parse($currentDate)->addDay()->format('Y-m-d');
            }
        }
        return $collection;
    }

    public function SearchAjax(Request $request)
    {
        /* Set up database */
        $config = Config::get('database.connections.sqlsrv');
        $config['database'] = "dtr_" . $request->filterBranch;
        config()->set('database.connections.sqlsrv', $config);
        DB::purge('sqlsrv');

        $userInfo = new UserInfo;

        if ($request->chooseFilterValue === "1") {
            $userId = $userInfo->GetUserId($request->filterInput)->get(); // Get userId of user
            $logs =  $userInfo->SelectLog()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)->OrderDate()->get();
        } elseif ($request->chooseFilterValue === "2") {
            $logs =  $userInfo->SelectLog()->JoinCol()->CompareDate($request->date1, $request->date2)->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
        } elseif ($request->chooseFilterValue === "3") {
            $logs = $userInfo->SelectLog()->where('name', 'like', '%'. $request->filterInput .'%')->JoinCol()->CompareDate($request->date1, $request->date2)->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
        }

        $collection = collect([]);
        $branchFilter = null;

        if ($request->chooseFilterValue === "1") {
            $collection = $this->dataHRISId($logs, $collection, $request->date1, $request->date2);
        } elseif ($request->chooseFilterValue === "2" || $request->chooseFilterValue === "3") {
            if ($request->chooseFilterValue === "2") {
                $branchFilter = 1; // BRANCH IS SELECTED
            }
            $collection = $this->dataBranchEmployeeName($logs, $collection, $request->date1, $request->date2);
        }

        return response()->json(['filterType' => $request->chooseFilterValue, 'collection' => $collection, 'branchFilter' => $branchFilter]);
    }

    private function getExplodeDate($data)
    {
        return explode(" ", $data);
    }
}
