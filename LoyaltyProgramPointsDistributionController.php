<?php
namespace App\Http\Controllers;


use App\Models\SystemConfiguration;
use Illuminate\Http\Request;
use App\Models\LpEmailTemplate;
use App\Models\LpPointsDistribution;
use App\Models\AccessPoint;
use App\Transformers\LpPointsDistributionTransformer;

class LpPointsDistributionController extends Controller
{
    public function index(){

        try {
            //Points distribution
            $allPointsDistribution = LpPointsDistribution::all();
            $allPointsDistribution =  fractal()
                ->collection($allPointsDistribution)
                ->transformWith(new LpPointsDistributionTransformer())
                ->toArray();

            //Application Option
            $app = SystemConfiguration::where('Name', 'loyalty program')->first();

            //Sign up bonus option
            $bonusOption = SystemConfiguration::where('Name', 'Loyalty Program Signup Bonus Options')->first();
            $bonusStatus = SystemConfiguration::where('Name', 'Loyalty Program Signup Bonus')->first();

            $bonusValue = explode("/",$bonusOption->Value);
            $emailTemplate = LpEmailTemplate::find($bonusValue[1]);

            $bonusData = ['points'                  => $bonusValue[0],
                          'templateIdSelected'      => $bonusValue[1],
                          'templateNameSelected'    => isset($emailTemplate->name)? $emailTemplate->name : '',
                          'enable'                  => ($bonusStatus->Value == 0)? false : true
                    ];

            //Cashless option
            $option = SystemConfiguration::where('Name', 'Cashless Option')->first();
            $enable = SystemConfiguration::where('Name', 'Cashless rule')->first();

            $cashlessValue = explode("/",$option->Value);
            $data = ['points' => $cashlessValue[0],
                       'cash' => $cashlessValue[1],
                     'enable' => ($enable->Value == 0)? false : true];

            //Access points scanner list
            $accessPointName = AccessPoint::all();
            $templates = LpEmailTemplate::select('id','name') ->get();

            $data_toReturn = [
                'app'                => $app->Value,
                'bonus'              => $bonusData,
                'cashless'           => $data,
                'points'             => $allPointsDistribution,
                'accessList'         => $accessPointName,
                'templates'          => $templates
            ];

            return response($data_toReturn, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }



    public function store(Request $request){

        try {
            $params = $request->all();
            $points = $params['points'];
            $accessPointId = $params['access_point_id'];
            $limit = $params['limit_per_day'];


            // Validating request
            $this->validate($request, [
                'access_point_id' => 'required'
            ]);

            //Check access point exist
            $accessPoint = AccessPoint::find($accessPointId);
            if (is_null($accessPoint)) {
                return response(['error' => 'No Access Point found'],
                    400);
            }

            $data_toReturn =[];

            $accessPointsTrashed = LpPointsDistribution::onlyTrashed()->where('access_point_id', $accessPointId)->first();
            $accessPoints = LpPointsDistribution::where('access_point_id', $accessPointId)->first();

            if(!empty($accessPoints) && empty($alertEmailTrashed)){
                return response(['error' => 'The points is already distributed'], 404);
            }

            if(empty($accessPoints) && !empty($accessPointsTrashed)){
                LpPointsDistribution::onlyTrashed()->where('access_point_id', $accessPointId)->update(['points'=>$points,'limit_per_day'=>$limit,'deleted_at' => NULL]);

                $data_toReturn = [
                    'id'                  => $accessPointsTrashed->id,
                    'access_point_name'   => $accessPoint['Name'],
                    'access_point_id'     => $accessPointId,
                    'points'              => $points,
                    'limit_per_day'       => $limit,
                    'deleted_at'          => NULL
                ];
            }

            if(empty($accessPointsTrashed) && empty($accessPoints)){
                $pointsDistribution = new LpPointsDistribution([
                    'points' => $points,
                    'access_point_id' => $accessPointId,
                    'limit_per_day' => $limit
                ]);

                $pointsDistribution->save();
                $pointsDistribution['access_point_name'] = $accessPoint['Name'];
                $data_toReturn= $pointsDistribution;
            }


            return response($data_toReturn, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }



    public function update(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];
            $accessPointId = $params['access_point_id'];

            $this->validate($request, [
                'id' => 'required'
            ]);

            $accessPoint = AccessPoint::find($accessPointId);
            if (is_null($accessPoint)) {
                return response(['error' => 'No access points found'],
                        400);
            }


            $info = [];
            foreach ($params as $key => $value) {
                $info[$key] = $value;
            }

            $pointsDistribution = LpPointsDistribution::find($id);
            $pointsDistribution->update($info);
            $pointsDistribution['access_point_name'] = $accessPoint['Name'];

            return response( $pointsDistribution, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }



    public function destory(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];

            $tier = LpPointsDistribution::find($id);
            if (is_null($tier)) {
                return response(['error' => 'No points distribution found for this id'],
                    404);
            }

            $tier->delete();
            return response()->json( 'success', 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function getCashlessOption(){

        try {
            $option = SystemConfiguration::where('Name', 'Cashless Option')->first();
            $enable = SystemConfiguration::where('Name', 'Cashless rule')->first();
            return response( ['value' => $option->Value, 'enable' => $enable->Value], 200);
        }catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function updateCashlessOption(Request $request){

        try {
            $params = $request->all();
            $points = $params['points'];
            $cash   = $params['cash'];


            $value = $points.'/'.$cash;
            $option = SystemConfiguration::where('Name', 'Cashless Option');
            $option->update(['Value' => $value]);

            return response('success', 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }

    public function updateCashlessEnable(Request $request){

        try{
            $params = $request->all();
            $value = $params['value'];

            SystemConfiguration::where('Name', 'Cashless rule')->update(['Value' => $value]);
            return response( 'success', 200);

        }catch (\Exception $e){
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function getBonusOption(){

        try {
            $option = SystemConfiguration::where('Name', 'Loyalty Program Signup Bonus Options')->first();
            $enable = SystemConfiguration::where('Name', 'Loyalty Program Signup Bonus')->first();
            return response( ['value' => $option->Value, 'enable' => $enable->Value], 200);
        }catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function updateBonusOption(Request $request){

        try {
            $params = $request->all();
            $points = $params['points'];
            $templateId   = $params['templateId'];


            $value = $points.'/'.$templateId;
            $option = SystemConfiguration::where('Name', 'Loyalty Program Signup Bonus Options');
            $option->update(['Value' => $value]);

            $emailTemplate = LpEmailTemplate::find($templateId)->toArray();

            $data_toReturn = [
                     'points'               => $points,
                     'templateIdSelected'   => $templateId,
                     'templateNameSelected' => $emailTemplate['name'],
            ];

            return response($data_toReturn, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }

    public function updateBonusStatus(Request $request){

        try{
            $params = $request->all();
            $value = $params['value'];

            SystemConfiguration::where('Name', 'Loyalty Program Signup Bonus')->update(['Value' => $value]);
            return response( 'success', 200);

        }catch (\Exception $e){
            return response(['error' => $e->getMessage()], 400);
        }
    }


    public function updateAppStatus(Request $request){

        try{
            $params = $request->all();
            $value = $params['value'];

            SystemConfiguration::where('Name', 'loyalty program')->update(['Value' => $value]);
            return response( 'success', 200);

        }catch (\Exception $e){
            return response(['error' => $e->getMessage()], 400);
        }
    }


    public function getAccessPoint(){

        try{
            $accessPoint  = AccessPoint::all();
            return response( $accessPoint, 200);

        }catch (\Exception $e){
            return response(['error' => $e->getMessage()], 400);
        }
    }



    public function getAvailableAccessPointsList(){

        try{


            $allPointsDistribution = LpPointsDistribution::all();
            $allPointsDistribution =  fractal()
                ->collection($allPointsDistribution)
                ->transformWith(new LpPointsDistributionTransformer())
                ->toArray();



            $selectedNames = [];
            foreach ($allPointsDistribution as $PointsDistribution){
                $selectedNames[] = $PointsDistribution['access_point_name'];
            }

            $accessPoint  = AccessPoint::all()->toArray();

            $filterNames=[];
            foreach ($accessPoint as $ap){
                if(!in_array($ap['Name'], $selectedNames)){
                    $filterNames[] = $ap;
                }

            }


            return response( $filterNames, 200);

        }catch (\Exception $e){
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function checkAccessExit(Request $request){

        try{
            $params = $request->all();
            $templateId = $params['id'];

            $allPointsDistribution = LpPointsDistribution::all();
            $allPointsDistribution =  fractal()
                ->collection($allPointsDistribution)
                ->transformWith(new LpPointsDistributionTransformer())
                ->toArray();



            $selectedNames = [];
            foreach ($allPointsDistribution as $PointsDistribution){
                $selectedNames[] = $PointsDistribution['access_point_id'];
            }

            if(in_array($templateId, $selectedNames)){
                return response(['error' => 'This Access Points Scanner is already distributed'], 404);
            }


            return response($templateId, 200);

        }catch (\Exception $e){
            return response(['error' => $e->getMessage()], 400);
        }
    }




}

