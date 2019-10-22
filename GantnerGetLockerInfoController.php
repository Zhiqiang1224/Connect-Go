<?php
/**
 * Created by PhpStorm.
 * Author: zhiqiang yang
 * Date: 2018-11-23
 * Time: 8:46 AM
 */

namespace App\Http\Controllers;
use App\Services\GantnerService\GatServices;
use App\Http\Traits\GatTrait;
use App\Http\Traits\Convert;
use Illuminate\Http\Request;

class GantnerGetLockerInfoController extends Controller
{
    use GatTrait;
    use Convert;
    protected $services;
    protected $dataToReturn;
    protected $lockerNumber;
    protected $lockerValidFrom;
    protected $lockerValidUntil;

    public function __construct(GatServices $services)
    {
        $this->services = $services;
    }


    /**
     * @param Request $request
     * @return array
     */
    public function getCard(Request $request)
    {
        try {
            $params = $request->all();
            $lockerNumber = $params['lockerNumber'];

            $checkLockerExist = $this->services->checkLockerExist();
            if(!in_array($params['lockerNumber'], $checkLockerExist)){
                return response()->json(['error' => "Unknown Locker"], 404);
            }

            $this->dataToReturn['locker'] = [];
            $this->dataToReturn['card'] = [];

            $getAuthResponse = $this->services->sentGetAuthorizationsRequest(null);
            if (!$getAuthResponse['AuthorizationTags']) {
                return response()->json(['error' => "There is no authorization"], 404);
            }

            foreach ($getAuthResponse['AuthorizationTags'] as $authorizationTag) {
                if ($this->hasThisLocker($authorizationTag, $lockerNumber)) {
                    $this->dataToReturn['card'][] = [
                        'lockerNumber'               => $this->lockerNumber,
                        'uid'                        => $this->decToHex($authorizationTag['CardUID']),
                        'firstName'                  => $authorizationTag['FirstName'],
                        'lastName'                   => $authorizationTag['LastName'],
                        'uidValidFrom'               => $this->toLocalTimeShow($authorizationTag['ValidFrom']),
                        'uidValidUntil'              => $this->toLocalTimeShow($authorizationTag['ValidUntil']),
                        'assignedValidFrom'          => $this->toLocalTimeShow($this->lockerValidFrom),
                        'assignedValidUntil'         => $this->toLocalTimeShow($this->lockerValidUntil),
                        'expLocker'                  => $this->checkTimeExp($this->lockerValidUntil)
                    ];
                }
            }

            return response($this->dataToReturn['card'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param $authorizationTag
     * @param $lockerNumber
     * @return bool
     */
    public function hasThisLocker($authorizationTag, $lockerNumber)
    {
        $hasThisLocker = false;

        foreach ($authorizationTag['LockerAuthorizations'] as $locker) {
            if ($lockerNumber  == $locker['LockerNumber']){
                $hasThisLocker = true;
                    $this->dataToReturn['locker'] = [
                        'lockerNumber'      => $locker['LockerNumber'],
                        'lockerRecordId'    => $locker['LockerRecordId'],
                        'lockerGroupName'   => $locker['LockerGroupName']
                    ];
                $this->lockerNumber = $locker['LockerNumber'];
                $this->lockerValidFrom = $locker['ValidFrom'];
                $this->lockerValidUntil = $locker['ValidUntil'];
            }
        }
        return $hasThisLocker;
    }
}