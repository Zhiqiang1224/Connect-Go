<?php
/**
 * Created by PhpStorm.
 * Author: zhiqiang yang
 * Date: 2018-11-23
 * Time: 8:46 AM
 */

namespace App\Http\Controllers;
use App\Services\GantnerService\GatServices;
use App\Http\Traits\Convert;
use Illuminate\Http\Request;

class GantnerRemoveCardFromUserController extends Controller
{
    use Convert;
    protected $services;

    public function __construct(GatServices $services) {
        $this->services = $services;
    }


    /**
     * @param Request $request
     * @return array
     */
    public function removeLockerFromUser(Request $request){
        try {
            $params = $request->all();
            $uid = $this->hexToDec($params['uid']);

            $checkUidExist = $this->services->checkCardExist();
            if(!in_array($uid, $checkUidExist)){
                return response()->json(['error' => "Unknown UID"], 404);
            }

            $checkLockerExist = $this->services->checkLockerExist();
            if(!in_array($params['lockerNumber'], $checkLockerExist)){
                return response()->json(['error' => "Unknown Locker Number"], 404);
            }


            $dataToReturn['tags'] = [
                'authorizationGroupRecordId' => '',
                'uid'                        => '',
                'firstName'                  => '',
                'lastName'                   => '',
                'recordId'                   => '',
                'tagValidFrom'               => '',
                'tagValidUntil'              => '',
                'lockerRecordId'             => '',
                'lockerValidFrom'            => '',
                'lockerValidUntil'           => '',
                'lockerGroupName'            => '',
                'lockerRid'                  => ''
            ];

            $dataToReturn['locker'] = [
                'lockerNumber'               => '',
                'lockerRecordId'             => '',
                'lockerValidFrom'            => '',
                'lockerValidUntil'           => '',
                'lockerGroupName'            => '',
                'lockerRid'                  => ''
            ];

            //Get the authorization
            $getAuthResponse = $this->services->sentGetAuthorizationsRequest($uid);

            if (!$getAuthResponse['AuthorizationTags']) {
                return response()->json(['error' => "Authorization is empty"], 404);
            }

            $lockersNumber = [];
            foreach ($getAuthResponse['AuthorizationTags'] as $authorizationTag) {
                $dataToReturn['tags'] = [
                    'authorizationGroupRecordId' => $authorizationTag['AuthorizationGroupRecordId'],
                    'uid'                        => $authorizationTag['CardUID'],
                    'firstName'                  => $authorizationTag['FirstName'],
                    'lastName'                   => $authorizationTag['LastName'],
                    'recordId'                   => $authorizationTag['RecordId'],
                    'tagValidFrom'               => $authorizationTag['ValidFrom'],
                    'tagValidUntil'              => $authorizationTag['ValidUntil']
                ];

                if (!$authorizationTag['LockerAuthorizations']) {
                    return response()->json(['error' => "There is no locker for this {$authorizationTag['FirstName']}"], 400);
                }

                foreach ($authorizationTag['LockerAuthorizations'] as $locker) {
                    $lockersNumber[] = $locker['LockerNumber'];

                    if ($params['lockerNumber'] == $locker['LockerNumber']) {
                        $dataToReturn['locker'] = [
                            'lockerNumber'     => $locker['LockerNumber'],
                            'lockerRecordId'   => $locker['LockerRecordId'],
                            'lockerValidFrom'  => $locker['ValidFrom'],
                            'lockerValidUntil' => $locker['ValidUntil'],
                            'lockerGroupName'  => $locker['LockerGroupName'],
                            'lockerRid'        => $locker['RecordId']
                        ];
                    }
                }
            }

            if(!in_array($params['lockerNumber'], $lockersNumber)){
                return response()->json(['error' => "Unknown locker ID for {$getAuthResponse['AuthorizationTags'][0]['FirstName']}"], 400);
            }

            $response = array_merge($dataToReturn['tags'], $dataToReturn['locker']);
            $response = $this->services->sendSaveDataCarrierRequestForRemove($response);

            return response($response, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }
    

}