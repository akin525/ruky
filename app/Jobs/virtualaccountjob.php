<?php

namespace App\Jobs;

use App\Mail\Emailotp;
use App\Models\transaction;
use App\Models\User;
use App\Models\wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class virtualaccountjob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $wallet = wallet::where('username', $this->user->username)->first();

        //
        $username=$this->user['username'].rand(111, 999);

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
            CURLOPT_POSTFIELDS => array('account_name' => $this->user['name'],
                'business_short_name' => 'EFE','uniqueid' => $username,
                'email' => $this->user['email'],'dob' => $this->user['dob'],
                'address' => $this->user['address'],'gender' => $this->user['gender'],
                'phone' =>$this->user['phone'],'webhook_url' => 'https://app.efemobilemoney.com/api/run1'),
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
            $wallet->account_number = $number;
            $wallet->account_name= $account;
            $wallet->bank=$bank;
            $wallet->save();

        }elseif ($data['success']==0){


        }
        $transaction=transaction::create([
            'username'=>$this->user['username'],
            'activities'=>'Virtual Account Generated Successfully',
        ]);
        $input=$this->user;
        $receiver=$this->user['email'];
        $admin= 'info@efemobilemoney.com';
        Mail::to($receiver)->send(new Emailotp($input));
        Mail::to($admin)->send(new Emailotp($input));

    }
}
