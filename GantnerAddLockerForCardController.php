<?php
/**
 * Created by PhpStorm.
 * Author: zhiqiang yang
 * Date: 2018-12-01
 * Time: 8:46 AM
 */

namespace App\Http\Controllers;
use App\Http\Traits\GatTrait;
use App\Http\Traits\Convert;
use App\Services\GantnerService\GatServices;
use App\Services\GantnerService\GatInitCard;
use Illuminate\Http\Request;

class GantnerAddLockerForCardController extends Controller
{
    use GatTrait;
    use Convert;
    protected $services;
    protected $initCard;

    public function __construct(GatServices $services, GatInitCard $initCard) {
        $this->services = $services;
        $this->initCard = $initCard;
    }

    /**
     * Add a card to a locker
     *
     * @param Request $request
     * @return array
     */
    public function addLockerForCard(Request $request) {
        try {

            $params = $request->all();

            $uid = $this->hexToDec($params['uid']);
            $number = $params['lockerNumber'];
            $startDate = $params['startDate'];
            $endDate = $params['endDate'];

            if(!$uid){
                return response()->json(['error' => "Invalid UID"], 404);
            }

            $this->validate($request, [
                'startDate' => 'required|date|before:endDate',
                'endDate'   => 'required|date|after:startDate'
            ]);

            $iniCard = [
                'uid'              => '',
                'firstName'        => '',
                'lastName'         => '',
                'lockerType'       => ''
            ];

            $data_toReturn['locker'] = [
                'number'           => '',
                'lockerRecordId'   => '',
                'lockerGroupName'  => '',
                'recordId'         => ''
            ];

            $data_toReturn['tags'] = [
                'authorizationGroupRecordId'      => '',
                'uid'                             => '',
                'Info1'                           => '',
                'Info2'                           => '',
                'firstName'                       => '',
                'lastName'                        => '',
                'authRecordIdTag'                 => '',
                'tagValidFrom'                    => '',
                'tagValidUntil'                   => '',
                'maxAllowedLockers'               => '',
                'maxAllowedLockersPerLockerGroup' => ''
            ];


            /***********************************************************************************************************
             * 1: Check the basic information for the locker
             ***********************************************************************************************************/
            $checkLockerExist = $this->services->checkLockerExist();
            if(!in_array($params['lockerNumber'], $checkLockerExist)){
                return response()->json(['error' => "Unknown Locker Number"], 404);
            }

            if($params['share']=='false'){
                // Check the locker status
                $shareLocker = $this->initCard->getConfigurationForLocker('Locker Sharing Restriction');
                if($shareLocker['value'] === 'invitee' || $shareLocker['value'] === 'owner'){
                    if($this->services->getCountUidByLocker($number)!= 0){
                        return response()->json(['error' => "The locker is in use"], 404);
                    }
                }
            }

            /***********************************************************************************************************
             * 2: Prepare the information of the locker
             ***********************************************************************************************************/
            $getLockerWithNumber = $this->services->sendGetLockersRequest($number);
            if (!isset($getLockerWithNumber['Lockers']) || empty($getLockerWithNumber['Lockers'])) {
                return response()->json(['error' => "Server delay 1"], 404);
            }

            // Get the information of locker
            foreach ($getLockerWithNumber['Lockers'] as $locker){
                if($number === $locker['Number']){
                    $data_toReturn['locker'] = [
                        'number'          => $number,
                        'lockerRecordId'  => $locker['RecordId'],
                        'lockerGroupName' => $locker['LockerGroupName'],
                        'maxAllowedCards' => 0,
                        'recordId'        => $this->getLockerAuthorizationId()
                    ];
                }
            }

            // Check if the locker info is match from the order
            if($params['share']=='false'){
                $CheckOrder = $this->initCard->getConfigurationForLocker('Locker Order Check');
                if($CheckOrder['value']){
                    $lockerType = $data_toReturn['locker']['lockerGroupName'];
                    $lockerAccount = $this->services->getCountLockerByUid($uid);
                    $getOrder = $this->initCard->getOrderForLocker($uid);

                    // Check if the uid has the order
                    if ($getOrder['largeLocker'] == '' && $getOrder['smallLocker'] == '') {
                        return response()->json(['error' => "There is no locker order with this card"], 404);
                    }

                    // Check the the max number of order
                    if(!empty($getOrder['total'])){
                        if($lockerAccount['totalLockerAccount'] >= $getOrder['total']){
                            return response()->json(['error' => "The card reach the maximum order of locker"], 404);
                        }
                    }

                    if($lockerType === 'Family'){
                       if(empty($getOrder['largeLocker'])){
                           return response()->json(['error' => "The locker type is not match with order type"], 404);
                       }

                       if($lockerAccount['bigLockerAccount'] >= $getOrder['largeLocker']) {
                           return response()->json(['error' => "Reached max larger order locker"], 404);
                       }
                    }

                    if($lockerType === 'Double'){
                        if(empty($getOrder['smallLocker'])){
                            return response()->json(['error' => "The locker type is not match with order type"], 404);
                        }

                        if($lockerAccount['smallLockerAccount'] >= $getOrder['smallLocker']) {
                            return response()->json(['error' => "Reached max small order locker"], 404);
                        }
                    }
                }
            }

            /***********************************************************************************************************
             * 3: Initial the card if it's not exist in the server
             ***********************************************************************************************************/
            $checkUidExist = $this->services->checkCardExist();
            if(!in_array($uid, $checkUidExist)){

                $iniCard = $this->initCard->init($uid);
                if(!$iniCard){
                    return response()->json(['error' => "initialization failed"], 404);
                }
            }

            /***********************************************************************************************************
             * 4: Prepare the information from the authorization
             ***********************************************************************************************************/
            $getAuthResponse = $this->services->sentGetAuthorizationsRequest($uid);
            if (!isset($getAuthResponse['AuthorizationTags']) || empty($getAuthResponse['AuthorizationTags'])) {
                return response()->json(['error' => "Server delay2"], 404);
            }

            // Get the information of UID
            $lockersNumber = [];
            foreach ($getAuthResponse['AuthorizationTags'] as $authorizationTag) {
                $data_toReturn['tags'] = [
                    'authorizationGroupRecordId'      => $authorizationTag['AuthorizationGroupRecordId'],
                    'uid'                             => !empty($iniCard['uid'])? $iniCard['uid'] : $uid,
                    'info1'                           => $authorizationTag['Info1'],
                    'info2'                           => $authorizationTag['Info2'],
                    'firstName'                       => !empty($iniCard['firstName'])? $iniCard['firstName'] : $authorizationTag['FirstName'],
                    'lastName'                        => !empty($iniCard['lastName'])? $iniCard['lastName'] : $authorizationTag['LastName'],
                    'authRecordIdTag'                 => $authorizationTag['RecordId'],
                    'validFrom'                       => $startDate,
                    'validUntil'                      => $endDate,
                    'maxAllowedLockers'               => $authorizationTag['MaxAllowedLockers'],
                    'maxAllowedLockersPerLockerGroup' => $authorizationTag['MaxAllowedLockersPerLockerGroup']
                ];

                if($authorizationTag['LockerAuthorizations']){
                    foreach ($authorizationTag['LockerAuthorizations'] as $locker) {
                        $lockersNumber[] = $locker['LockerNumber'];
                    }
                }
            }

            if(in_array($number, $lockersNumber)){
                return response()->json(['error' => "The Card is already assigned to this locker"], 404);
            }

            // Assign the card to the locker
            $dataReturn = array_merge($data_toReturn['tags'],  $data_toReturn['locker']);

            /***********************************************************************************************************
             * 5: sent save data request for assign
             ***********************************************************************************************************/
            $response= $this->services->sendSaveDataCarrierRequestForAssign($dataReturn);

            if (!$response) {
                return response()->json(['error' => "Bad request for sending Save Data Carrier Request"], 404);
            }

            return response($response, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

}