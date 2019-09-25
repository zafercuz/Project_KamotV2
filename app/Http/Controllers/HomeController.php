<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserInfo;
use App\CheckInOut;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index()
    {
        // $from = date('2019-05-02');
        $from = Carbon::createFromDate(2019, 05, 20)->format('Y-m-d');
        $to = Carbon::createFromDate(2019, 05, 22)->format('Y-m-d');
        // $to = date('2019-05-09');

        // $logIn = UserInfo::SelectLogIn()->JoinCol()->HrisId(1)->where('checktime', '>=', $from)->where('checktime', '<=', $to)->TimeType('i')->OrderDate()->get();
        $logIn = UserInfo::SelectLogIn()->JoinCol()->HrisId(1)->CompareDate($from, $to)->TimeType('i')->OrderDate()->get();
        $logOut = UserInfo::SelectLogOut()->JoinCol()->HrisId(1)->CompareDate($from, $to)->TimeType('o')->OrderDate()->get();

        $collection = collect([]);

        if (count($logIn) < count($logOut)) {
            $size = count($logOut);
        } elseif (count($logIn) > count($logOut)) {
            $size = count($logIn);
        } else {
            $size = count($logIn);
        }

        for ($count = 0, $counter1 = 0, $counter2 = 0; $count < $size; $count++, $counter1++, $counter2++) {
            if (isset($logIn[$counter1]->checktime)) {
                $Date1 = explode(" ", $logIn[$counter1]->checktime);
            } else {
                $Date1 = null;
            }
            
            if (isset($logOut[$counter2]->checktime)) {
                $Date2 = explode(" ", $logOut[$counter2]->checktime);
            } else {
                $Date2 = null;
            }
            

            if ($Date1 != null && $Date2 != null) {
                if ($Date1[0] == $Date2[0]) {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                } elseif ($Date1[0] > $Date2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date2[0],
                        "N/A",
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                    $counter1 -= 1;
                    $size += 1;
                } elseif ($Date1[0] < $Date2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG IN
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        "N/A",
                        $logIn[$counter1]->name
                    );
                    $counter2 -= 1;
                    $size += 1;
                }

                // $prevLogInDate = [$Date1];
                // $prevLogOutDate = [$Date2];
            } elseif ($Date1 == null && $Date2 != null) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1-1]->userid,
                    $Date2[0],
                    "N/A",
                    $logOut[$counter2]->checktime,
                    $logIn[$counter1-1]->name
                );
            } elseif ($Date1 != null && $Date2 == null) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $Date1[0],
                    $logIn[$counter1]->checktime,
                    "N/A",
                    $logIn[$counter1]->name
                );
            }
        }
        // dd($logIn, $logOut, $collection);


        /*
        for ($counter1 = 0, $counter2 = 0, $loopCounter = 0; $counter1 < count($logIn) && $counter2 < count($logOut); $counter1++, $counter2++, $loopCounter++) {
            $Date1 = explode(" ", $logIn[$counter1]->checktime);
            $Date2 = explode(" ", $logOut[$counter2]->checktime);

            // $this->pushCollection($collection, $logIn[$counter1]->userid, $Date1[0], $logIn[$counter1]->checktime,
            //         $logOut[$counter2]->checktime, $logIn[$counter1]->name);

            if ($Date1[0] == $Date2[0]) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $Date1[0],
                    $logIn[$counter1]->checktime,
                    $logOut[$counter2]->checktime,
                    $logIn[$counter1]->name
                );
            } elseif ($Date1[0] > $Date2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                // if(($Date2[0] == explode(" ", $logOut[$counter2-1]->checktime)[0]) && ($Date2[1] > explode(" ", $logOut[$counter2-1]->checktime)[1])){
                //     $collection->replace([$loopCounter-1 => ['logOut' => $logOut[$counter2]->checktime]]);
                // }
                // else {
                $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date2[0],
                        "N/A",
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                $counter1 -= 1;
            // }
            } elseif ($Date1[0] < $Date2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG IN
                // if(($Date1[0] == explode(" ", $logIn[$counter1-1]->checktime)[0]) && ($Date1[1] > explode(" ", $logIn[$counter1-1]->checktime)[1])){
                //     // DO NOTHING AND DONT PUSH
                // }
                // else {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        "N/A",
                        $logIn[$counter1]->name
                    );
                $counter2 -= 1;
                // }
            }
        } */

        return view('index');
    }

    public function SearchAjax(Request $request)
    {
        $userId = UserInfo::GetUserId($request->filterInput)->get();
        $logIn = UserInfo::SelectLogIn()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)
        ->TimeType('i')->OrderDate()->get();
        $logOut = UserInfo::SelectLogOut()->JoinCol()->HrisId($userId[0]->userid)->CompareDate($request->date1, $request->date2)
        ->TimeType('o')->OrderDate()->get();

        $collection = collect([]);
        
        if (count($logIn) < count($logOut)) {
            $size = count($logOut);
        } elseif (count($logIn) > count($logOut)) {
            $size = count($logIn);
        } else {
            $size = count($logIn);
        }

        for ($count = 0, $counter1 = 0, $counter2 = 0; $count < $size; $count++, $counter1++, $counter2++) {
            if (isset($logIn[$counter1]->checktime)) {
                $Date1 = explode(" ", $logIn[$counter1]->checktime);
            } else {
                $Date1 = null;
            }
            
            if (isset($logOut[$counter2]->checktime)) {
                $Date2 = explode(" ", $logOut[$counter2]->checktime);
            } else {
                $Date2 = null;
            }
            

            if ($Date1 != null && $Date2 != null) {
                if ($Date1[0] == $Date2[0]) {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        Carbon::parse($logIn[$counter1]->checktime)->format('h:i:s A'),
                        Carbon::parse($logOut[$counter2]->checktime)->format('h:i:s A'),
                        $logIn[$counter1]->name
                    );
                } elseif ($Date1[0] > $Date2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date2[0],
                        "N/A",
                        Carbon::parse($logOut[$counter2]->checktime)->format('h:i:s A'),
                        $logIn[$counter1]->name
                    );
                    $counter1 -= 1;
                    $size += 1;
                } elseif ($Date1[0] < $Date2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG IN
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        Carbon::parse($logIn[$counter1]->checktime)->format('h:i:s A'),
                        "N/A",
                        $logIn[$counter1]->name
                    );
                    $counter2 -= 1;
                    $size += 1;
                }

                // $prevLogInDate = [$Date1];
                // $prevLogOutDate = [$Date2];
            } elseif ($Date1 == null && $Date2 != null) { // IF DATE 1 IS MISSING
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1-1]->userid,
                    $Date2[0],
                    "N/A",
                    Carbon::parse($logOut[$counter2]->checktime)->format('h:i:s A'),
                    $logIn[$counter1-1]->name
                );
            } elseif ($Date1 != null && $Date2 == null) { // IF DATE 2 IS MISSING
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $Date1[0],
                    Carbon::parse($logIn[$counter1]->checktime)->format('h:i:s A'),
                    "N/A",
                    $logIn[$counter1]->name
                );
            }
        }
    
        return response()->json(['collection' => $collection, 'userid' => $userId]);
    }

    private function pushCollection($collection, $userid, $date, $logIn, $logOut, $name)
    {
        $collection->push([
            'userid' => $userid,
            'date' => $date,
            'logIn' => $logIn,
            'logOut' => $logOut,
            'name' => $name,
        ]);
    }
}
