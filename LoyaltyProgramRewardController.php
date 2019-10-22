<?php
namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\AddOnField;
use App\Models\LpLoyaltyTier;
use App\Models\LpEmailTemplate;
use App\Models\SystemConfiguration;
use Illuminate\Http\Request;
use App\Models\LpPointsDistribution;
use App\Models\AccessPoint;
use App\Models\LpEmail;
use App\Models\LpEmailRewardMapping;
use App\Transformers\LpPointsDistributionTransformer;
use App\Models\LpReward;
use App\Transformers\LpRewardTransformer;

class LpRewardController extends Controller
{
    public function index(){

        try {
            $allRewards = LpReward::all();
            $allRewards =  fractal()
                ->collection($allRewards)
                ->transformWith(new LpRewardTransformer())
                ->toArray();

            usort($allRewards, function ($a, $b) {
                return $a['points'] - $b['points'];
            });

            $app = SystemConfiguration::where('Name', 'loyalty program')->first();
            $templates = LpEmailTemplate::select('id','name') ->get();

            $allAddOn = AddOnField::all();

            $data_toReturn = [
                'app'            => $app->Value,
                'rewards'        => $allRewards,
                'templates'      => $templates,
                'addOns'         => $allAddOn
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
            $addOnFieldId = $params['add_on_field_id'];
            $emailTemplateId = $params['email_template_id'];
            $patronFlag = $params['patron_email_flag'];
            $destinationEmail = $params['destinationEmail'];

            // Validating request
            $this->validate($request, [
                'points'            => 'required',
                'patron_email_flag' => 'required'
            ]);

            if(isset($addOnFieldId) && !empty($addOnFieldId)) {
                $addOn = AddOnField::find($addOnFieldId);
                if (is_null($addOn)) {
                    return response(['error' => 'Unknown Add On'],
                        400);
                }
            }

            if(isset($emailTemplateId) && !empty($emailTemplateId)) {
                $template = LpEmailTemplate::find($emailTemplateId);
                if (is_null($template)) {
                    return response(['error' => 'Unknown email template'],
                        400);
                }
            }

            $lpReward = new LpReward([
                'points'            => $points,
                'add_on_field_id'   => !empty($addOnFieldId)? $addOnFieldId : NULL,
                'patron_email_flag' => $patronFlag,
                'email_template_id' => !empty($emailTemplateId)? $emailTemplateId : NULL
            ]);

            $lpReward->save();

            if(!$patronFlag){

                if(!$destinationEmail){
                    return response(['error' => 'Please Input The Destination Email'], 400);
                }

                $destinationEmails = explode(';', $destinationEmail);
                foreach ($destinationEmails as $destinationEmail){
                    $email = new  LpEmail([
                        'email' => $destinationEmail
                    ]);
                    $email->save();

                    $emailMapping = new LpEmailRewardMapping([
                        'lp_reward_id'   => $lpReward->id,
                        'lp_email_id'    => $email->id
                    ]);

                    $emailMapping->save();
                }

            }

            $data_toReturn = [
                'id'                     => $lpReward->id,
                'points'                 => $params['points'],
                'add_on_name'            => isset($addOn->name)? $addOn->name: '',
                'email_name'             => isset($template->name)? $template->name : '',
                'destination_email'      => isset($params['destinationEmail'])? $params['destinationEmail']: 'Patron'
            ];

            return response($data_toReturn, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }


    public function update(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];

            $this->validate($request, [
                'id' => 'required',
                'patron_email_flag' => 'required'
            ]);

            //Delete the exist emails
            $mappings = LpEmailRewardMapping::where('lp_reward_id',$params['id'])->get();

            if ($mappings) {
                foreach ($mappings as $mapping){
                    $mapping->where('lp_reward_id', $params['id'])->delete();
                    $mail = LpEmail::where('id',$mapping['lp_email_id'])->get()->first();
                    if(NULL!== $mail){
                        $mail->delete();
                    }
                }
            }

            //Insert new emails
            if(!$params['patron_email_flag']){

                if(!$params['destinationEmail']){
                    return response(['error' => 'Please Input The Destination Email'], 400);
                }

                $destinationEmails = explode(';', $params['destinationEmail']);
                foreach ($destinationEmails as $destinationEmail){
                        $email = new  LpEmail([
                            'email' => $destinationEmail
                        ]);
                    $email->save();

                    $emailMapping = new LpEmailRewardMapping([
                        'lp_reward_id'   => $params['id'],
                        'lp_email_id'    => $email->id
                    ]);

                    $emailMapping->save();
                }
            }

            $info = [];
            foreach ($params as $key => $value) {
                $info[$key] = $value;
            }

            $reward = LpReward::find($id);
            $reward->update($info);

            if(NULL!== $reward['email_template_id']){
                $template = LpEmailTemplate::find($reward['email_template_id']);
            }

            if(NULL!== $reward['add_on_field_id']){
                $addOn = AddOnField::find($reward['add_on_field_id']);
            }

            $data_toReturn = [
                'id'                     => $reward['id'],
                'points'                 => $reward['points'],
                'patron_email_flag'      => $reward['patron_email_flag'],
                'add_on_name'            => isset($addOn->name)? $addOn->name : '',
                'email_name'             => isset($template->name)? $template->name : '',
                'destination_email'      => isset($params['destinationEmail'])? $params['destinationEmail']: 'Patron',
                'email_template_id'      => $reward['email_template_id']
            ];

            return response($data_toReturn, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }


    public function destory(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];

            $reward = LpReward::find($id);
            if (is_null($reward)) {
                return response(['error' => 'No Loyalty Reward found for this id'],
                    404);
            }

            $reward->delete();
            return response()->json( 'success', 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

}