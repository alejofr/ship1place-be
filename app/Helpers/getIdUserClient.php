<?php

namespace App\Helpers;


class getIdUserClient{
    public static function getIdUser($user) : string
    {
        $userId = $user->user_id;

        if( $user->user_id_parent !== null ){
            $userId = $user->user_id_parent;
        }

        return $userId;
    }
}