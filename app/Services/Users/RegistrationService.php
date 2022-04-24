<?php

namespace App\Services\Users;

use App\Services\BaseServiceInterface;
use DB;
use App\Models\User;

class RegistrationService implements BaseServiceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        return $this->processInvite();
    }

    private function processInvite()
    {
        return  \DB::transaction(function () {
            $new_user = $this->createUser($this->data);
            return $new_user;
        });
    }


    private function createUser($data)
    {
        $user = new User();
        $user->id = uniqid();
        $user->phone = $data['phone'];
        $user->viewer_id = $data['viewer_id'];
        $user->save();
        return $user;
    }




}
