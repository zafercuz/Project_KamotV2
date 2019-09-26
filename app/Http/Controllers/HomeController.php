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
        $from = Carbon::createFromDate(2019, 5, 02)->format('Y-m-d');
        $to = Carbon::createFromDate(2019, 06, 06)->format('Y-m-d');

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
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                } elseif ($Date1[0] > $Date2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                    if ($Date2[0] == $prevLogOutDate[0][0] && $Date2[1] > $prevLogOutDate[0][1]) {
                        $filt = $collection->replaceRecursive([$count-2 => ['logOut' => $logOut[$counter2]->checktime]]);
                        $collection = $filt;
                    } else {
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $Date2[0],
                            "N/A",
                            $logOut[$counter2]->checktime,
                            $logIn[$counter1]->name
                        );
                    }
                    $counter1 -= 1;
                    $size += 1;
                } elseif ($Date1[0] < $Date2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG IN
                if ($Date1[0] != $prevLogInDate[0][0]) { // INSERT ONLY IF NOT MULTIPLE LOG IN
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        "N/A",
                        $logIn[$counter1]->name
                    );
                }
                    $counter2 -= 1;
                    $size += 1;
                }

                $prevLogInDate = [$Date1];
                $prevLogOutDate = [$Date2];
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

        $collection = $this->removeLastItem($collection, $logOut);

        // for ($count = 0; $count < count($collection); $count++) {
        //     if ($collection[$count]['logOut'] != 'N/A' && $collection[$count]['logIn'] != 'N/A') {
        //         $parseLogs = $collection->replaceRecursive(
        //         [
        //         $count => ['logOut' => Carbon::parse($collection[$count]['logOut'])->format('h:i:s A')],
        //         $count => ['logIn' => Carbon::parse($collection[$count]['logIn'])->format('h:i:s A')],
        //         ]
        //     );
        //         $collection = $parseLogs;
        //     }
        // }

        // dd($logIn, $logOut, $collection, $size);

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
        // Carbon::parse($logIn[$counter1]->checktime)->format('h:i:s A')
        
        if (count($logIn) < count($logOut)) {
            $size = count($logOut);
        } elseif (count($logIn) > count($logOut)) {
            $size = count($logIn);
        } else {
            $size = count($logIn);
        }
        
        for ($count = 0, $counter1 = 0, $counter2 = 0; $count < $size; $count++, $counter1++, $counter2++) {
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
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        $logOut[$counter2]->checktime,
                        $logIn[$counter1]->name
                    );
                } elseif ($Date1[0] > $Date2[0]) { // EMPLOYEE DIDN'T LOG IN OR MULTIPLE LOG OUT
                    if ($Date2[0] == $prevLogOutDate[0][0] && $Date2[1] > $prevLogOutDate[0][1]) {
                        $filt = $collection->replaceRecursive([$count-2 => ['logOut' => $logOut[$counter2]->checktime]]);
                        $collection = $filt;
                    } else {
                        $this->pushCollection(
                            $collection,
                            $logIn[$counter1]->userid,
                            $Date2[0],
                            "N/A",
                            $logOut[$counter2]->checktime,
                            $logIn[$counter1]->name
                        );
                    }
                    $counter1 -= 1;
                    $size += 1;
                } elseif ($Date1[0] < $Date2[0]) { // EMPLOYEE DIDN'T LOG OUT OR MULTIPLE LOG IN
                if ($Date1[0] != $prevLogInDate[0][0]) { // INSERT ONLY IF NOT MULTIPLE LOG IN
                    $this->pushCollection(
                        $collection,
                        $logIn[$counter1]->userid,
                        $Date1[0],
                        $logIn[$counter1]->checktime,
                        "N/A",
                        $logIn[$counter1]->name
                    );
                }
                    $counter2 -= 1;
                    $size += 1;
                }

                $prevLogInDate = [$Date1];
                $prevLogOutDate = [$Date2];
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
    
        $collection = $this->removeLastItem($collection, $logOut);

        return response()->json(['logIn' => $logIn, 'collection' => $collection, 'userid' => $userId]);
    }

    private function pushCollection($collection, $userid, $date, $logIn, $logOut, $name)
    {
        $collection->push([
            'userid' => $userid,
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
        if ($collection[$collSize-1]['logIn'] == "N/A" &&
            $this->getExplodeDate($collection[$collSize-1]['logOut'])[0] == $this->getExplodeDate($collection[$collSize-2]['logOut'])[0]
            && $this->getExplodeDate($collection[$collSize-1]['logOut'])[1] > $this->getExplodeDate($collection[$collSize-2]['logOut'])[1]) { // MULTIPLE LOG OUT
            
            $change = $collection->replaceRecursive([$collSize-2 => ['logOut' => $logOut[$collSize-1]->checktime]]);
            $collection = $change;
            $collection->forget($collSize-1);
        } elseif ($collection[$collSize-1]['logOut'] == "N/A" &&
                $this->getExplodeDate($collection[$collSize-1]['logIn'])[0] == $this->getExplodeDate($collection[$collSize-2]['logIn'])[0]) {
            $collection->forget($collSize-1);
        }
        return $collection;
    }
}
