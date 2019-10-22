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
use App\Services\GantnerService\GatInitCard;
use App\Http\Traits\Convert;
use Illuminate\Http\Request;

class GantnerGetCardInfoController extends Controller
{
    use Convert;
    use GatTrait;
    protected $services;
    protected $initCard;


    public function __construct(GatServices $services, GatInitCard $initCard) {
        $this->services = $services;
        $this->initCard = $initCard;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCardInfo(Request $request){
        try {
            $params = $request->all();

            $dataToReturn['card'] = [
                'uid'                        => '',
                'firstName'                  => '',
                'lastName'                   => '',
                'uidValidFrom'               => '',
                'uidValidUntil'              => '',
                'largeLocker'                => 0,
                'smallLocker'                => 0,
                'expCard'                    => false,
                'shareLocker'                => '',
                'picture'                    => ''
            ];

            $dataToReturn['locker'] = [
                'lockerNumber'               => '',
                'lockerGroupName'            => '',
                'lockerValidFrom'            => '',
                'lockerValidUntil'           => '',
                'expLocker'                  => '',
                'checkOwner'                 => false,
            ];

            $uid = $this->hexToDec($params['uid']);

            if(!$uid){
                return response()->json(['error' => "Invalid UID"], 404);
            }

            // Get the order info by uid
            $getOrder = $this->initCard->getOrderForLocker($uid);
            $shareLocker = $this->initCard->getConfigurationForLocker('Locker Sharing Restriction');
            $picture = $this->initCard->getPictureUrl($params['uid']);

            $checkUidExist = $this->services->checkCardExist();
            if(!in_array($uid, $checkUidExist)){
                $dataToReturn['card'] = [
                    'uid'                        => $params['uid'],
                    'firstName'                  => !empty($getOrder['firstName'])? $getOrder['firstName'] : 'N/A',
                    'lastName'                   => !empty($getOrder['lastName'])? $getOrder['lastName'] : 'N/A',
                    'largeLocker'                => !empty($getOrder['largeLocker'])? $getOrder['largeLocker'] : 0,
                    'smallLocker'                => !empty($getOrder['smallLocker'])? $getOrder['smallLocker'] : 0,
                    'uidValidFrom'               => 'N/A',
                    'uidValidUntil'              => 'N/A',
                    'picture'                    => !empty($picture)? $picture[0]['url'] : ''
                ];

                return response($dataToReturn, 200);
            }

            $dataToReturn['locker'] = [];

            $getAuthResponse = $this->services->sentGetAuthorizationsRequest($uid);

            if (!$getAuthResponse['AuthorizationTags']) {
                return response()->json(['error' => "Server delay,Please try again"], 404);
            }

            foreach ($getAuthResponse['AuthorizationTags'] as $authorizationTag) {
                $dataToReturn['card'] = [
                    'uid'                        => $this->decToHex($authorizationTag['CardUID']),
                    'firstName'                  => !empty($authorizationTag['FirstName'])? $authorizationTag['FirstName'] : 'N/A',
                    'lastName'                   => !empty($authorizationTag['LastName'])? $authorizationTag['LastName'] : 'N/A',
                    'uidValidFrom'               => $this->toLocalTimeShow($authorizationTag['ValidFrom']),
                    'uidValidUntil'              => $this->toLocalTimeShow($authorizationTag['ValidUntil']),
                    'largeLocker'                => !empty($getOrder['largeLocker'])? $getOrder['largeLocker'] : 0,
                    'smallLocker'                => !empty($getOrder['smallLocker'])? $getOrder['smallLocker'] : 0,
                    'expCard'                    => $this->checkTimeExp($authorizationTag['ValidUntil']),
                    'shareLocker'                => $shareLocker['value'],
                    'picture'                    => !empty($picture)? $picture[0]['url'] : ''
                ];

                if ($authorizationTag['LockerAuthorizations']) {
                    foreach ($authorizationTag['LockerAuthorizations'] as $locker) {
                        $dataToReturn['locker'][] = [
                            'lockerNumber'      => isset($locker['LockerNumber']) ? $locker['LockerNumber'] : null,
                            'lockerGroupName'   => isset($locker['LockerGroupName']) ? $locker['LockerGroupName'] : null,
                            'lockerValidFrom'   => isset($locker['ValidFrom']) ? $this->toLocalTimeShow($locker['ValidFrom']) : null,
                            'lockerValidUntil'  => isset($locker['ValidUntil']) ? $this->toLocalTimeShow($locker['ValidUntil']) : null,
                            'expLocker'         => isset($locker['ValidUntil']) ? $this->checkTimeExp($locker['ValidUntil']) : null,
                            'checkOwner'        => $this->services->getFirstUidByLocker($locker['LockerNumber'],$dataToReturn['card']['uid'])
                        ];
                    }
                }
            }

            return response($dataToReturn, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }


}