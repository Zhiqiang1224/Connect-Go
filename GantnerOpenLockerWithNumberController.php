<?php


namespace App\Http\Controllers;
use App\Services\GantnerService\GatServices;
use Illuminate\Http\Request;

class GantnerOpenLockerWithNumberController extends Controller
{
    protected $services;

    public function __construct(GatServices $services) {
        $this->services = $services;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function open(Request $request){
        try {
            $params = $request->all();

            $checkLockerExist = $this->services->checkLockerExist();
            if(!in_array($params['lockerNumber'], $checkLockerExist)){
                return response()->json(['error' => "Unknown locker Number"], 404);
            }

            $getLockerResponse = $this->services->sendGetLockersRequest($params['lockerNumber']);

            if (($getLockerResponse['Lockers'][0]['State']) !== 3) {
                return response()->json(['error' => "The locker is already open"], 404);
            }

            $result = $this->services->sendGetExecuteLockerActionRequest($params['lockerNumber']);

            if (!$result) {
                return response()->json('Locker open failed', 400);
            }

            return response($result, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }

    /**
     * @param Request $request
     * @return array
     */
    public function close(Request $request){
        try {
            $params = $request->all();

            $checkLockerExist = $this->services->checkLockerExist();
            if(!in_array($params['lockerNumber'], $checkLockerExist)){
                return response()->json(['error' => "Unknown locker Number"], 404);
            }

            $getLockerResponse = $this->services->sendGetLockersRequest($params['lockerNumber']);

            $recordId = $getLockerResponse['Lockers'][0]['RecordId'];

            if (($getLockerResponse['Lockers'][0]['State']) === 3) {
                return response()->json(['error' => "The locker is already closed"], 404);
            }

            $result = $this->services->closeLockRequestWithNumber($params['lockerNumber'], $recordId);

            if (!$result) {
                return response()->json('Locker close failed', 400);
            }

            return response($result, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }

}



