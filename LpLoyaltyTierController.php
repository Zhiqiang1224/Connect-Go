<?php
namespace App\Http\Controllers;

use App\Models\LpLoyaltyTier;
use App\Models\LpEmailTemplate;
use App\Models\SystemConfiguration;
use Illuminate\Http\Request;
use App\Transformers\LpLoyaltyTierTransformer;

class LpLoyaltyTierController extends Controller
{
    public function index(){

        try {
            $allTiers = LpLoyaltyTier::all();
            $allTiers =  fractal()
                ->collection($allTiers)
                ->transformWith(new LpLoyaltyTierTransformer())
                ->toArray();

            $app = SystemConfiguration::where('Name', 'loyalty program')->first();
            $templates = LpEmailTemplate::select('id','name')  ->get();

            $data_toReturn = [
                'app'          => $app->Value,
                'tiers'        => $allTiers,
                'templates'    => $templates,
            ];

            return response($data_toReturn, 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }



    public function store(Request $request){

        try {
            $params = $request->all();
            $name = $params['name'];
            $points = $params['points'];
            $templateId = $params['templateId'];

            // Validating request
            $this->validate($request, [
               // 'templateId' => 'required',
                'name' => 'required'
            ]);

            if(isset($templateId) && !empty($templateId)){
                $emailTemplate = LpEmailTemplate::find($templateId)->toArray();
                if (is_null($emailTemplate)) {
                    return response(['error' => 'No Email Template found'], 404);
                }
            }

            $tierPoints = LpLoyaltyTier::where('points_required',$points)->get()->toArray();
            if($tierPoints){
                return response(['error' => 'The points value is already exist'], 400);
            }

            $tier = new LpLoyaltyTier([
                'points_required'   => $points,
                'name'              => $name,
                'email_template_id' => isset($templateId)? $templateId : NULL
            ]);

            $tier->save();
            $tier['email_template_name'] = isset($emailTemplate['name'])? $emailTemplate['name'] : NULL;

            return response( $tier, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }



    public function update(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];
            $templateId = $params['email_template_id'];

            $this->validate($request, [
                'id' => 'required'
            ]);

            if(isset($templateId) && !empty($templateId)) {
                $emailTemplate = LpEmailTemplate::find($templateId);
                if (is_null($emailTemplate)) {
                    return response(['error' => 'No Email Template found'], 400);
                }
            }

            $info = [];
            foreach ($params as $key => $value) {
                $info[$key] = $value;
            }

            $tier = LpLoyaltyTier::find($id);
            $tier->update($info);

            $tier['email_template_name'] = isset($emailTemplate['name'])? $emailTemplate['name'] : NULL;

            return response( $tier, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }



    public function destory(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];

            $tier = LpLoyaltyTier::find($id);
            if (is_null($tier)) {
                return response(['error' => 'No Loyalty Tier found for this id'],
                    404);
            }

            $tier->delete();
            return response()->json( 'success', 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }
}

