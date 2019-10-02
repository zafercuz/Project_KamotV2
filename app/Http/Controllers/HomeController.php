<?php

namespace App\Http\Controllers;

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
        
        $from = Carbon::createFromDate(2019, 8, 2)->format('Y-m-d');
        $to = Carbon::createFromDate(2019, 10, 2)->format('Y-m-d');

        $dbId = "000";
        $config = Config::get('database.connections.sqlsrv');
        $config['database'] = "dtr_" . $dbId;
        config()->set('database.connections.sqlsrv', $config);
        DB::purge('sqlsrv');

        $userInfo = new UserInfo;

        $logIn = $userInfo->SelectLog()->where('name', 'like', '%BRENDA%')->JoinCol()->CompareDate($from, $to)->TimeType('i')->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
        $logOut = $userInfo->SelectLog()->where('name', 'like', '%BRENDA%')->JoinCol()->CompareDate($from, $to)->TimeType('o')->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
        
        // $logIn = $userInfo->SelectLog()->JoinCol()->HrisId(148)->CompareDate($from, $to)->TimeType('i')->OrderDate()->get();
        // $logOut = $userInfo->SelectLog()->JoinCol()->HrisId(148)->CompareDate($from, $to)->TimeType('o')->OrderDate()->get();
        // dd($logIn, $logOut);

        $collection = collect([]);
        $move = 0;
        $prevLogInDate = null;
        $prevLogOutDate = null;

        if (count($logIn) < count($logOut)) {
            $size = count($logOut);
        } elseif (count($logIn) > count($logOut)) {
            $size = count($logIn);
        } else {
            $size = count($logIn);
        }

        // $collection = $this->forHRISId($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate);
        $collection = $this->forEmployeeName($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate);
        // dd($collection);
        $collection = $this->removeLastItem($collection, $logOut);
        dd($logIn, $logOut, $collection, $size, $move);

        return view('index', compact('branch'));
    }

    public function forHRISId($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate)
    {
        for ($count = 0, $counter1 = 0, $counter2 = 0; $count < $size+$move; $count++, $counter1++, $counter2++) {
            if (isset($logIn[$counter1]->checktime)) {
                $Date1 = $this->getExplodeDate($logIn[$counter1]->checktime);
            } else {
                $Date1 = null;
            }
        
            if (isset($logOut[$counter2]->checktime)) {
                $Date2 = $this->getExplodeDate($logOut[$counter2]->checktime);
            } else {
                $Date2 = null;
            }

            if ($Date1 != null && $Date2 != null) {
                if ($Date1[0] == $Date2[0]) {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $logIn[$counter1]->Badgenumber,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                } elseif ($Date1[0] > $Date2[0]) { // DIDN'T LOG IN OR MULTIPLE LOG OUT
                    // if ($Date2[0] == $prevLogOutDate[0][0] && $Date2[1] > $prevLogOutDate[0][1]) {
                    //     // $move += 1;
                    //     $filt = $collection->replaceRecursive([$count-$move => ['logOut' => $logOut[$counter2]->checktime]]);
                    //     // $filt = $collection->replaceRecursive([$count-1 => ['logOut' => $logOut[$counter2]->checktime]]);
                    //     $move += 1;
                    //     $collection = $filt;
                    //     // dd($move);
                    //     // $collection[$count-1]['logOut'] = $logOut[$counter2]->checktime;
                    //     // dd($collection[$count-1]);
                    // } else {
                    $this->pushCollection(
                        $collection,
                        $logOut[$counter2]->userid,
                        $logOut[$counter2]->Badgenumber,
                        $Date2[0],
                        "N/A",
                        $logOut[$counter2]->checktime,
                        $logOut[$counter2]->name
                    );
                    $move += 1;
                    // }
                    $counter1 -= 1;
                } elseif ($Date1[0] < $Date2[0]) { // DIDN'T LOG OUT OR MULTIPLE LOG IN
                    if ($Date1[0] != $prevLogInDate[0][0]) {
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $logIn[$counter1]->Badgenumber,
                            $Date1[0],
                            $logIn[$counter1]->checktime,
                            "N/A",
                            $logIn[$counter1]->name
                        );
                    }
                    $move += 1;
                    $counter2 -= 1;
                }
                $prevLogInDate = [$Date1];
                $prevLogOutDate = [$Date2];
            } elseif ($Date1 == null && $Date2 != null) {
                $this->pushCollection(
                    $collection,
                    $logOut[$counter2]->userid,
                    $logOut[$counter2]->Badgenumber,
                    $Date2[0],
                    "N/A",
                    $logOut[$counter2]->checktime,
                    $logOut[$counter2]->name
                );
            } elseif ($Date1 != null && $Date2 == null) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $logIn[$counter1]->Badgenumber,
                    $Date1[0],
                    $logIn[$counter1]->checktime,
                    "N/A",
                    $logIn[$counter1]->name
                );
            }
        }
        // dd($collection);
        return $collection;
    }

    public function forEmployeeName($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate)
    {
        for ($count = 0, $counter1 = 0, $counter2 = 0; $count < $size+$move; $count++, $counter1++, $counter2++) {
            if (isset($logIn[$counter1]->checktime)) {
                $Date1 = $this->getExplodeDate($logIn[$counter1]->checktime);
                $currentId = $logIn[$counter1]->userid;
            } else {
                $Date1 = null;
                $currentId = null;
            }
        
            if (isset($logOut[$counter2]->checktime)) {
                $Date2 = $this->getExplodeDate($logOut[$counter2]->checktime);
                $currentId = $logOut[$counter2]->userid;
            } else {
                $Date2 = null;
                $currentId = null;
            }

            if ($Date1 != null && $Date2 != null) {
                if ($Date1[0] == $Date2[0]) {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $logIn[$counter1]->Badgenumber,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                } elseif ($Date1[0] > $Date2[0]) {
                    if ($Date1[0] == $prevLogInDate[0][0]) { // MULTIPLE LOG IN, DONT PUSH TO COLLECTION
                        $counter2 -= 1;
                    } elseif ($Date2[0] == $prevLogOutDate[0][0]) { // IS IT MULTIPLE LOG OUT?
                        $this->pushCollection(
                            $collection,
                            $logOut[$counter2]->userid,
                            $logOut[$counter2]->Badgenumber,
                            $Date2[0],
                            "wat",
                            $logOut[$counter2]->checktime,
                            $logOut[$counter2]->name
                        );
                        $counter1 -= 1;
                    } 
                    else { // DIDN'T LOG OUT
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $logIn[$counter1]->Badgenumber,
                            $Date1[0],
                            $logIn[$counter1]->checktime,
                            "awd",
                            $logIn[$counter1]->name
                        );
                        $counter2 -= 1;
                    }
                    // dd($collection,$prevLogOutDate);
                    $move += 1;
                } 
                elseif ($Date1[0] < $Date2[0]) {
                    // dd($prevLogOutDate, $count, $counter1, $counter2, explode(" ", $logOut[$counter2-1]['checktime'])[0]);
                    // $prevLogOutDate = explode(" ", $logOut[$counter2-1]['checktime'])[0];
                    if ($Date1[0] == $prevLogInDate[0][0]) { // MULTIPLE LOG IN
                        $counter2 -= 1;
                    } elseif ($Date2[0] == $prevLogOutDate[0][0]) { // MULTIPLE LOG OUT
                        $this->pushCollection(
                            $collection,
                            $logOut[$counter2]->userid,
                            $logOut[$counter2]->Badgenumber,
                            $Date2[0],
                            "N/A",
                            $logOut[$counter2]->checktime,
                            $logOut[$counter2]->name
                        );
                        $counter1 -= 1;
                    } 
                    else {
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $logIn[$counter1]->Badgenumber,
                            $Date1[0],
                            $logIn[$counter1]->checktime,
                            "N/A",
                            $logIn[$counter1]->name
                        );
                        $counter2 -= 1;
                    }
                    $move += 1;
                }
                $prevLogInDate = [$Date1];
                $prevLogOutDate = [$Date2];
            } elseif ($Date1 == null && $Date2 != null) {
                $this->pushCollection(
                    $collection,
                    $logOut[$counter2]->userid,
                    $logOut[$counter2]->Badgenumber,
                    $Date2[0],
                    "N/A",
                    $logOut[$counter2]->checktime,
                    $logOut[$counter2]->name
                );
            } elseif ($Date1 != null && $Date2 == null) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $logIn[$counter1]->Badgenumber,
                    $Date1[0],
                    $logIn[$counter1]->checktime,
                    "N/A",
                    $logIn[$counter1]->name
                );
            }
        }
        // dd($collection);
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
            $logIn = $userInfo->SelectLog()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)
            ->TimeType('i')->OrderDate()->get();
            $logOut = $userInfo->SelectLog()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)
            ->TimeType('o')->OrderDate()->get();
        } elseif ($request->chooseFilterValue === "3") {
            $logIn = $userInfo->SelectLog()->where('name', 'like', '%' . $request->filterInput . '%')->JoinCol()->CompareDate($request->date1, $request->date2)->TimeType('i')->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
            $logOut = $userInfo->SelectLog()->where('name', 'like', '%' . $request->filterInput . '%')->JoinCol()->CompareDate($request->date1, $request->date2)->TimeType('o')->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
        }
        // Carbon::parse($logIn[$counter1]->checktime)->format('h:i:s A')

        $collection = collect([]);
        $move = 0;
        $prevLogInDate = null;
        $prevLogOutDate = null;

        if (count($logIn) < count($logOut)) {
            $size = count($logOut);
        } elseif (count($logIn) > count($logOut)) {
            $size = count($logIn);
        } else {
            $size = count($logIn);
        }

        if ($request->chooseFilterValue === "1") {
            $collection = $this->forHRISId($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate);
        } elseif ($request->chooseFilterValue === "3") {
            $collection = $this->forEmployeeName($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate);
        }
        
        $collection = $this->removeLastItem($collection, $logOut);

        return response()->json(['filterType' => $request->chooseFilterValue, 'collection' => $collection]);
    }

    public function pushCollection($collection, $userid, $badgeNumber, $date, $logIn, $logOut, $name)
    {
        $collection->push([
            'userid' => $userid,
            'badgeNumber' => $badgeNumber,
            'date' => $date,
            'logIn' => $logIn,
            'logOut' => $logOut,
            'nameOrId' => $name,
        ]);
    }

    private function getExplodeDate($data)
    {
        return explode(" ", $data);
    }

    private function removeLastItem($collection, $logOut)
    {
        $collSize = $collection->count();
        // dd($collection, $collSize);
        // if ($collection[$collSize-1]['logIn'] == "N/A" &&
        //     $this->getExplodeDate($collection[$collSize-1]['logOut'])[0] == $this->getExplodeDate($collection[$collSize-2]['logOut'])[0]
        //     && $this->getExplodeDate($collection[$collSize-1]['logOut'])[1] > $this->getExplodeDate($collection[$collSize-2]['logOut'])[1]) { // MULTIPLE LOG OUT
            
        //     $change = $collection->replaceRecursive([$collSize-2 => ['logOut' => $logOut[$collSize-1]->checktime]]);
        //     // dd($logOut[$collSize]);
        //     $collection = $change;
        //     $collection->forget($collSize-1);
        // } elseif ($collection[$collSize-1]['logOut'] == "N/A" &&
        //         $this->getExplodeDate($collection[$collSize-1]['logIn'])[0] == $this->getExplodeDate($collection[$collSize-2]['logIn'])[0]) {
        //     $collection->forget($collSize-1);
        // }
        if ($collection[$collSize-1]['logOut'] == "N/A" &&
                $this->getExplodeDate($collection[$collSize-1]['logIn'])[0] == $this->getExplodeDate($collection[$collSize-2]['logIn'])[0]) {
            $collection->forget($collSize-1);
        }
        return $collection;
    }
}
