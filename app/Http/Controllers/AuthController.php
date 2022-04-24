<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Storage;
use Carbon\Carbon;
use Auth;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function create(Request $request)
        {
            $this->validate($request, [
                'email' => 'required|email|unique:users',
                'phone' => 'required|numeric|digits:11|unique:users',
                'countryCode' => 'required|string',
                'password' => 'required|string',
            ]);

            try{

                $checkPhoneNumber = User::where('phone', $request->phone)->first();

                if($checkPhoneNumber)
                {
                    $message = 'You are a registered Customer';
                    $data = null;
                    return response()->json(['error' => false, 'is_verified' => true, 'status_code' => 200, 'message' => $message], 200);
                }

                   if(!is_null($checkPhoneNumber))
                   {
                       $message = 'Phone Number already verified, redirect to create Password';
                       $res = true;
                       $check = null;
                       $data['message'] = $message;
                       $data['res'] = $res;
                       $data['data'] = $check;

                       return $data;
                   }


                        $message = 'Customer Registration OTP sent';
                        $res = false;
                        $code = rand(100,1000000);
                        // $userIDGen = mt_rand(100000000, 999999999);
                        $currentTime = Carbon::now()->addDay();

                        $createACustomer = User::create([
                            // 'vi' => $userIDGen,
                            'phone' => $request->phone,
                            'email' => $request->email,
                            'countryCode' => $request->countryCode,
                            'code' => $code,
                            'expiry'=>$currentTime,
                            'is_verified' => true,
                            'status' => 1,
                            'password' => Hash::make($request->password),
                        ]);

                        $phone = '234'.substr($createACustomer->phone, 1);
                        $message = "Congratulations! You have been registered on KazanTv Portal. Kindly proceed to setup your profile";

                        $this->notifyApplicantBySMS($phone, $message);
                        // Mail::to($request->email)->send(new WelcomeAgent($request->email, $request->first_name, $request->last_name,$request->password));


            }catch (\Exception $exception){
                return response()->json(['error' => true, 'message' => $exception->getMessage()], 500);
            }
            return response()->json(['error' => false, 'message' => 'Customer Created', 'data' => $createACustomer], 200);
        }



         public function notifyApplicantBySMS($phone, $message)
        {
            try {
                $res = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post('https://api.ng.termii.com/api/sms/send', [
                    "api_key" => env('TERMI_API_KEY'),
                    "message_type" => "NUMERIC",
                    "to" => $phone,
                    "from" => "KanazTV",
                    "channel" => "dnd",
                    "type" => "plain",
                    "sms" => $message,
                ]);

            }catch (\Exception $exception) {
                return response()->json(['status' => false, 'message' => $exception->getMessage()], 500);
            }
            $response = json_decode($res->getBody()->getContents(), true);
            return $response;
        }






}
