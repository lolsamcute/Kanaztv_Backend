<?php

namespace App\Services\Users;


use App\Services\BaseServiceInterface;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateService implements BaseServiceInterface
{
    protected $user_details;

    public function __construct($user_details)
    {
        $this->user_details = $user_details;
    }

    public function run()
    {
        return DB::transaction(function () {
            return User::updateOrCreate(
                [
                    'phone' => $this->user_details['phone'],
                    'viewer_id' => $this->user_details['viewer_id'],
                    'password' => Hash::make($this->user_details['password'])
                ]
            );
        });

        // $user = User::where('phone', $this->user_details['phone'])->where('viewer_id', $this->user_details['viewer_id'])->first();

        // if ($user !== null) {
        //     $user->update(['name' => request('name')]);
        // } else {
        //     $user = User::create([
        //         'email' => request('email'),
        //         'name' => request('name'),
        //     ]);
        // }
    }
}
