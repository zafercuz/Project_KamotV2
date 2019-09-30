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
        // Config::set("database.connections.branch", ["database" => "zbranch_office"]);
        $branchModel->setConnection('branch');
        $branch = $branchModel->get();
        // dd($branch);

        $from = Carbon::createFromDate(2019, 9, 2)->format('Y-m-d');
        $to = Carbon::createFromDate(2019, 9, 4)->format('Y-m-d');

        $userInfo = new UserInfo;
        // $userInfo->setConnection('dtr001');

        // $logIn = $userInfo->SelectLog()->where('name', 'like', '%ann%')->JoinCol()->CompareDate($from, $to)->TimeType('i')->orderBy('checkinout.userid', 'asc')->OrderDate()->get();
        // $logOut = $userInfo->SelectLog()->where('name', 'like', '%ann%')->JoinCol()->CompareDate($from, $to)->TimeType('o')->orderBy('checkinout.userid', 'asc')->OrderDate()->get();
        // dd($logIn, $logOut);
        // $config = Config::get('database.connections.dtr001');
        // $config['database'] = "dtr001";
        // config()->set('database.connections.dtr001', $config);
        
        // Config::set("database.connections.sqlsrv", ["database" => "dtr001"]);
        $databaseName = "dtr001"; // Dynamically get this value from db
        $userInfo->setConnection($databaseName);
        $logIn = $userInfo->SelectLog()->JoinCol()->HrisId(1)->CompareDate($from, $to)->TimeType('i')->OrderDate()->get();
        $logOut = $userInfo->SelectLog()->JoinCol()->HrisId(1)->CompareDate($from, $to)->TimeType('o')->OrderDate()->get();
        // dd($logIn, $logOut);

        $collection = collect([]);
        $move = 0;
        $prevLogInDate = null;
        $prevLogOutDate = null;

        // dd($logIn, $logOut);

        if (count($logIn) < count($logOut)) {
            $size = count($logOut);
        } elseif (count($logIn) > count($logOut)) {
            $size = count($logIn);
        } else {
            $size = count($logIn);
        }

        $collection = $this->forHRISId($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate);
        // $collection = $this->forEmployeeName($logIn, $logOut, $collection, $size, $move, $prevLogInDate, $prevLogOutDate);
        dd($collection);
        $collection = $this->removeLastItem($collection, $logOut);
        // dd($logIn, $logOut, $collection, $size, $move);

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
                    if ($prevLogInDate == null && $prevLogOutDate == null) { // FOR THE FIRST ENTRY
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $logIn[$counter1]->Badgenumber,
                            $Date1[0],
                            $logIn[$counter1]->checktime,
                            $logOut[$counter2]->checktime,
                            $logIn[$counter1]->name
                        );
                    } elseif ($Date1[0] == $prevLogInDate[0][0] && $Date1[0] == $prevLogInDate[0][1]
                            && $Date2[0] == $prevLogOutDate[0][0] && $Date2[0] == $prevLogInDate[0][1]) {
                            // DO NOTHING
                    } elseif ($prevLogInDate != null && $prevLogOutDate != null && 
                            $Date1[0] != $prevLogInDate[0][0] && $Date1[0] != $prevLogInDate[0][1]
                            && $Date2[0] != $prevLogOutDate[0][0] && $Date2[0] != $prevLogInDate[0][1]) {
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $logIn[$counter1]->Badgenumber,
                            $Date1[0],
                            $logIn[$counter1]->checktime,
                            $logOut[$counter2]->checktime,
                            $logIn[$counter1]->name
                        );
                    }
                } elseif ($Date1[0] > $Date2[0]) { // DIDN'T LOG IN OR MULTIPLE LOG OUT
                    // if ($Date2[0] == $prevLogOutDate[0][0] && $Date2[1] > $prevLogOutDate[0][1]) {
                    //     // $move += 1;
                    //     $filt = $collection->replaceRecursive([$count-$move => ['logOut' => $logOut[$counter2]->checktime]]);
                    //     $collection = $filt;
                    // } else {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $logIn[$counter1]->Badgenumber,
                        $Date2[0],
                        "N/A",
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
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
                } elseif ($Date1[0] > $Date2[0]) { // DIDN'T LOG IN OR MULTIPLE LOG OUT
                    if ($Date1[0] == $prevLogInDate[0][0]) { // MULTIPLE LOG IN, DONT PUSH TO COLLECTION
                        $counter2 -= 1;
                    } elseif ($Date2[0] == $prevLogOutDate[0][0]) { // IS IT MULTIPLE LOG OUT?
                        $this->pushCollection(
                            $collection,
                            $logOut[$counter2]->userid,
                            $logOut[$counter2]->Badgenumber,
                            $Date2[0],
                            "N/A",
                            $logOut[$counter2]->checktime,
                            $logIn[$counter2]->name
                        );
                        $counter1 -= 1;
                    } else { // DIDN'T LOG IN
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
                    $move += 1;
                } elseif ($Date1[0] < $Date2[0]) { // DIDN'T LOG OUT OR MULTIPLE LOG IN
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
                    } else { // DIDN'T LOG OUT
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
        $userId = UserInfo::GetUserId($request->filterInput)->get();

        if ($request->chooseFilterValue === "1") {
            $logIn = UserInfo::SelectLog()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)
            ->TimeType('i')->OrderDate()->get();
            $logOut = UserInfo::SelectLog()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)
            ->TimeType('o')->OrderDate()->get();
        } elseif ($request->chooseFilterValue === "3") {
            $logIn = UserInfo::SelectLog()->where('name', 'like', '%' . $request->filterInput . '%')->JoinCol()->CompareDate($request->date1, $request->date2)->TimeType('i')->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
            $logOut = UserInfo::SelectLog()->where('name', 'like', '%' . $request->filterInput . '%')->JoinCol()->CompareDate($request->date1, $request->date2)->TimeType('o')->orderBy('userinfo.userid', 'asc')->OrderDate()->get();
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

        return response()->json(['filterType' => $request->chooseFilterValue, 'collection' => $collection, 'userid' => $userId]);
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
