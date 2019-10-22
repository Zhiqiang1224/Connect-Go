<?php
/**
 * Created by PhpStorm.
 * Author: zhiqiang yang
 * Date: 2018-11-23
 * Time: 8:46 AM
 */

namespace App\Http\Controllers;
use App\Http\Traits\GatTrait;
use App\Services\GantnerService\GatServices;
use App\Services\GantnerService\GatInitCard;
use App\Models\GantnerLocker;


class GantnerGetLockerStatusController extends Controller
{
    use GatTrait;
    protected $services;
    protected $initCard;

    public function __construct(GatServices $services, GatInitCard $initCard) {
        $this->services = $services;
        $this->initCard = $initCard;
    }


    /**
     * Get all the lockers state
     * @return array
     */
    public function getAllLockerState() {
        try {
            $getAuthResponse = $this->services->sentGetAuthorizationsRequest();
            if (!$getAuthResponse) {
                return response()->json(['error' => "Bad request for get authorization response"], 404);
            }
            $response = $this->services->sendGetLockersRequest();
            $states = [];
            for ($i = 0; $i < count($response['Lockers']); $i++) {
                $states[$i]['sn'] = $response['Lockers'][$i]['SerialNumber'];
                $states[$i]['ssn'] = $response['Lockers'][$i]['SlaveSerialNumber'];
                $states[$i]['name'] = $response['Lockers'][$i]['Number'];
                $states[$i]['room'] = $response['Lockers'][$i]['LockerGroupName'];
                $states[$i]['state'] = $response['Lockers'][$i]['State'];
                $states[$i]['lot'] = $this->toLocalTime($response['Lockers'][$i]['LastOpenedTime']);
                $states[$i]['lct'] = $this->toLocalTime($response['Lockers'][$i]['LastClosedTime']);
                $states[$i]['isExisting'] = ($response['Lockers'][$i]['IsExisting'] == true) ? 'Yes' : 'No';
                $states[$i]['mac'] = $this->getUIDCountForLocker($getAuthResponse, $response['Lockers'][$i]['Number']);
                $states[$i]['status'] = ($this->getUIDCountForLocker($getAuthResponse, $response['Lockers'][$i]['Number']) === 0) ? 'Free' : 'Assigned';
            }

            $shareLocker = $this->initCard->getConfigurationForLocker('Locker Sharing Restriction');
            foreach ($states as &$state) {
                if(!empty($shareLocker['value'])){
                    $state['shareLocker'] = $shareLocker['value'];
                }

                if ($state['state'] == 2) {
                    $state['state'] = 'Close';
                }

                if ($state['state'] == 3) {
                    $state['state'] = 'Open';
                }

            }

            usort($states, function ($a, $b) {
                return $a['name'] - $b['name'];
            });

            return response($states, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }


    }

    /**
     *  Save all the lockers into database
     * @return array
     */
    public function saveAllLockers(){
        try {
            $lockers = $this->services->sendGetLockersRequest();
            foreach ($lockers as $locker) {
                $data_ToInsert = [
                    'id'                    => $locker['id'],
                    'locker_group_id'       => $locker['LockerGroupId'],
                    'slave_serial_number'   => $locker['SlaveSerialNumber'],
                    'address'               => $locker['Address'],
                    'locker_controller_id'  => $locker['LockerControllerId'],
                    'number'                => $locker['Number'],
                    'state'                 => $locker['State'],
                    'max_allowed_cards'     => $locker['MaxAllowedCards'],
                    'card_uid_in_use'       => $locker['CardUIDInUse'],
                    'is_existing'           => $locker['IsExisting'],
                    'locker_mode'           => $locker['LockerMode'],
                    'locker_close_counter'  => $locker['LockerCloseCounter'],
                    'last_closed_time'      => $locker['LastClosedTime'],
                    'record_id'             => $locker['RecordId'],
                    'locker_type_id'        => $locker['LockerTypeId']
                ];

                $lockerToSave = new GantnerLocker($data_ToInsert);
                if (!$lockerToSave->save()) {
                    throw new \ErrorException('An error occurred during inserting  in the database', 400);
                }
            }
            // Return success
            return ['code' => 200, 'message' => 'success'];
        } catch (\ErrorException $e) {
            return ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }


    public function getLockerById($lockerId) {
        $locker = GantnerLocker::where('id', $lockerId)->get()->first();

        if(!$locker){
            throw new \ErrorException('Unknown locker Id', 400);
        }
        return ['code' => 200, 'message' => $locker];
    }

}