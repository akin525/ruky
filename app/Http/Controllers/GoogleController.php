<?php

namespace App\Http\Controllers;

use App\Console\encription;
use App\Mail\Emailotp;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle()
    {
        try {
            $user = Socialite::driver('google')->user();
            $picture=$user->getAvatar();

            // Check Userks Email If Already There
            $is_user = User::where('email', encription::encryptdata($user->getEmail()))->first();
            if(!$is_user){

                $saveUser = User::updateOrCreate([
                    'google_id' => $user->getId(),
                ],[
                    'username' => encription::encryptdata($user->getName().rand(111, 999)),
                    'name' => encription::encryptdata($user->getName()),
                    'email' =>encription::encryptdata($user->getEmail()),
                    'phone' =>encription::encryptdata('08140000000'),
                    'gender' =>'Male',
                    'dob' =>'06/14/1986',
                    'address' =>'Ikeja Lagos State',
                    'password' =>$user->getName().'@'.$user->getId(),
                    'profile_photo_path'=>$picture,
                ]);
                $wallet= wallet::create([
                    'username'=>$saveUser['username'],
                    'balance' => 0,
                ]);
                $myuser=encription::decryptdata($saveUser['username']);
                $input=[
                    'name'=>$user->getName(),
                    'username'=>$myuser,
                    'email'=>$user->getEmail(),
                    'password'=>$saveUser['password'],
                ];
                $receiver=$input ['email'];
                $admin= 'info@renomobilemoney.com';

                $username=$saveUser['username'];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://integration.mcd.5starcompany.com.ng/api/reseller/virtual-account3',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('account_name' => $user->getName(),
                        'business_short_name' => 'RENO','uniqueid' => $username,
                        'email' => $user->getEmail(),'dob' => '1997-03-13',
                        'address' => 'Ikeja Lagos State','gender' => 'male',
                        'phone' =>'08146328645','webhook_url' => 'https://renomobilemoney.com/api/run1'),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: mcd_key_aq9vGp2N8679cX3uAU7zIc3jQfd'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                $data = json_decode($response, true);
                if ($data['success']==1){
                    $account = $data["data"]["customer_name"];
                    $number = $data["data"]["account_number"];
                    $bank = $data["data"]["bank_name"];

                    $wallet->account_number1=$number;
                    $wallet->account_name1=$account;
                    $wallet->bank=$bank;
                    $wallet->save();


                }elseif ($data['success']==0){

                }

                Mail::to($receiver)->send(new Emailotp($input));
                Mail::to($admin)->send(new Emailotp($input));
            }else{
                $saveUser = User::where('email',  encription::encryptdata($user->getEmail()))->update([
                    'google_id' => $user->getId(),
                    'profile_photo_path'=>$picture,
                ]);
                $saveUser = User::where('email',encription::encryptdata($user->getEmail()))->first();
            }


            Auth::loginUsingId($saveUser->id);

            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
