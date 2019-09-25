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
        $from = Carbon::createFromDate(2019, 05, 2)->format('Y-m-d');
        $to = Carbon::createFromDate(2019, 05, 9)->format('Y-m-d');
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

        for ($count = 0, $x = 0, $y = 0; $count < $size; $count++, $x++, $y++) {
            $setIn = null;
            $setOut = null;
            if (isset($logIn[$x]->checktime)) {
                $setIn = 1;
                $compareDate1 = explode(" ", $logIn[$x]->checktime);
            } else {
                $setIn = 0;
                $compareDate1 = null;
            }
            if (isset($logOut[$y]->checktime)) {
                $setOut = 1;
                $compareDate2 = explode(" ", $logOut[$y]->checktime);
            } else {
                $setOut = 0;
                $compareDate2 = null;
            }

            
            if ($compareDate1 != null && $compareDate2 != null) {
                if ($compareDate1[0] == $compareDate2[0]) {
                    $this->pushCollection(
                        $collection,
                        $logIn[$x]->userid,
                        $compareDate1[0],
                        $logIn[$x]->checktime,
                        $logOut[$y]->checktime,
                        $logIn[$x]->name
                    );
                } elseif ($compareDate1[0] > $compareDate2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                    $this->pushCollection(
                        $collection,
                        $logIn[$x]->userid,
                        $compareDate2[0],
                        "N/A",
                        $logOut[$y]->checktime,
                        $logIn[$x]->name
                    );
                    $x -= 1;
                } elseif ($compareDate1[0] < $compareDate2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                    $this->pushCollection(
                        $collection,
                        $logIn[$x]->userid,
                        $compareDate1[0],
                        $logIn[$x]->checktime,
                        "N/A",
                        $logIn[$x]->name
                    );
                    $y -= 1;
                }
            } elseif ($compareDate1 == null && $compareDate2 != null) {
                $this->pushCollection(
                    $collection,
                    $logIn[$x-1]->userid,
                    $compareDate2[0],
                    "N/A",
                    $logOut[$y]->checktime,
                    $logIn[$x-1]->name
                );
            } elseif ($compareDate1 != null && $compareDate2 == null) {
                $this->pushCollection(
                    $collection,
                    $logIn[$x]->userid,
                    $compareDate1[0],
                    $logIn[$x]->checktime,
                    "N/A",
                    $logIn[$x]->name
                );
            }
        }
        dd($logIn, $logOut, $collection);


        /*
        for ($counter1 = 0, $counter2 = 0, $loopCounter = 0; $counter1 < count($logIn) && $counter2 < count($logOut); $counter1++, $counter2++, $loopCounter++) {
            $compareDate1 = explode(" ", $logIn[$counter1]->checktime);
            $compareDate2 = explode(" ", $logOut[$counter2]->checktime);

            // $this->pushCollection($collection, $logIn[$counter1]->userid, $compareDate1[0], $logIn[$counter1]->checktime,
            //         $logOut[$counter2]->checktime, $logIn[$counter1]->name);

            if ($compareDate1[0] == $compareDate2[0]) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $compareDate1[0],
                    $logIn[$counter1]->checktime,
                    $logOut[$counter2]->checktime,
                    $logIn[$counter1]->name
                );
            } elseif ($compareDate1[0] > $compareDate2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                // if(($compareDate2[0] == explode(" ", $logOut[$counter2-1]->checktime)[0]) && ($compareDate2[1] > explode(" ", $logOut[$counter2-1]->checktime)[1])){
                //     $collection->replace([$loopCounter-1 => ['logOut' => $logOut[$counter2]->checktime]]);
                // }
                // else {
                $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $compareDate2[0],
                        "N/A",
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                $counter1 -= 1;
            // }
            } elseif ($compareDate1[0] < $compareDate2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG OUT
                // if(($compareDate1[0] == explode(" ", $logIn[$counter1-1]->checktime)[0]) && ($compareDate1[1] > explode(" ", $logIn[$counter1-1]->checktime)[1])){
                //     // DO NOTHING AND DONT PUSH
                // }
                // else {
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $compareDate1[0],
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
        $logIn = UserInfo::SelectLogIn()->JoinCol()->HrisId(1)->CompareDate($request->date1, $request->date2)
        ->TimeType('i')->OrderDate()->get();
        $logOut = UserInfo::SelectLogOut()->JoinCol()->HrisId(1)->CompareDate($request->date1, $request->date2)
        ->TimeType('o')->OrderDate()->get();

        $collection = collect([]);

        for ($counter1 = 0, $counter2 = 0, $loopCounter = 0; ($counter1 < count($logIn)) && ($counter2 < count($logOut)); $counter1++, $counter2++, $loopCounter++) {
            $compareDate1 = explode(" ", $logIn[$counter1]->checktime);
            $compareDate2 = explode(" ", $logOut[$counter2]->checktime);
            
            // $this->pushCollection(
            //     $collection,
            //     $logIn[$counter1]->userid,
            //     $compareDate1[0],
            //     $logIn[$counter1]->checktime,
            //     $logOut[$counter2]->checktime,
            //     $logIn[$counter1]->name
            // );

            if ($compareDate1[0] == $compareDate2[0]) {
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $compareDate1[0],
                    explode(" ", $logIn[$counter1]->checktime)[1],
                    explode(" ", $logOut[$counter2]->checktime)[1],
                    $logIn[$counter1]->name
                );
            } elseif ($compareDate1[0] > $compareDate2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                $this->pushCollection(
                    $collection,
                    $logIn[$counter1]->userid,
                    $compareDate2[0],
                    "N/A",
                    explode(" ", $logOut[$counter2]->checktime)[1],
                    $logIn[$counter1]->name
                );
                $counter1 -= 1;
            // }
            } elseif ($compareDate1[0] < $compareDate2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG OUT
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $compareDate1[0],
                        explode(" ", $logIn[$counter1]->checktime)[1],
                        "N/A",
                        $logIn[$counter1]->name
                    );
                $counter2 -= 1;
                // }
            }
        }
    
        return response()->json(['logIn' => $logIn, 'logOut' => $logOut, 'collection' => $collection]);
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
