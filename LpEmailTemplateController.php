<?php
namespace App\Http\Controllers;

use App\Models\LpEmailTemplate;
use App\Models\SystemConfiguration;
use Illuminate\Http\Request;
use App\Transformers\LpLoyaltyTierTransformer;

class LpEmailTemplateController extends Controller
{
    public function index()
    {

        try {
            $allEmails = LpEmailTemplate::all('id', 'name','file_name','html_code','sender','subject'); //todo: if need email HTML template inside return value.
            $app = SystemConfiguration::where('Name', 'loyalty program')->first();

            $data_toReturn = [
                'app'             => $app->Value,
                'template'        => $allEmails
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
            $sender = $params['sender'];
            $subject = $params['subject'];
            $html_code = $request->file('html_code');
            $html_code = file_get_contents($html_code);

            // Validating request
            $this->validate($request, [
                'sender' => 'required',
                'name' => 'required',
                'subject' => 'required'
            ]);


            $emailTemplate = new LpEmailTemplate([
                'sender' => $sender,
                'name' => $name,
                'subject' => $subject,
                'html_code' => $html_code
            ]);

            $emailTemplate->save();

            return response( $emailTemplate, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }


    public function update(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];
            $name = $params['name'];
            $sender = $params['sender'];
            $subject = $params['subject'];
            $html_code = $request->file('html_code');
            $html_code = file_get_contents($html_code);

            $info = [];
            $info['name'] = $name;
            $info['sender'] = $sender;
            $info['subject'] = $subject;
            $info['html_code'] = $html_code;

            $this->validate($request, [
                'id' => 'required'
            ]);

            // Validating request
            if(!empty($id)) {
                $emailTemplateId = LpEmailTemplate::find($id);
                if (is_null($emailTemplateId)) {
                    return response(['error' => 'No Email Template found'],
                        400);
                }
            }

            $tier = LpEmailTemplate::find($id);
            $tier->update($info);

            return response( $tier, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }

    public function destory(Request $request){

        try {
            $params = $request->all();
            $id = $params['id'];

            $tier = LpEmailTemplate::find($id);
            if (is_null($tier)) {
                return response(['error' => 'No Email Template found for this id'],
                    404);
            }

            $tier->delete();
            return response()->json( 'success', 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }


}