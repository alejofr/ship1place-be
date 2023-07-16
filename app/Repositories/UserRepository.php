<?php

namespace App\Repositories;

use App\Models\User;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/

class UserRepository
{
    public $modelo;
    public $search;

    /**
     *  Instance for Model User 
    */

    public function user($select = ['*'])
    {
        return $this->modelo = User::select($select);
    }

    public function userByType($userType)
    {
        return $this->modelo = User::role($userType)->select(['*']);
    }

    /**
     *  Method queryRelation ORM 
    */

    public function queryRelation($field, $value)
    {
        $this->modelo->where($field, '=', $value);
    }

    /**
     *  Method query ORM 
    */

    public function query($query)
    {
        $this->search = $query;

        $this->modelo = $this->modelo->Where(function ($query) {
            $query->orWhere('first_name',  'LIKE', '%' . $this->search . '%')
                ->orWhere('last_name',  'LIKE', '%' . $this->search . '%')
                ->orWhere('email',  'LIKE', '%' . $this->search . '%')
                ->orWhere('user_id_parent',  'LIKE', '%' . $this->search . '%');
        });
    }

    /**
     *  Method limit ORM 
    */

    public function limit($limit = 30, $page = 1)
    {
        $this->modelo->limit($limit)
            ->skip($limit * ($page - 1));
    }

    /**
     *  Method count ORM 
    */

    public function count(): int
    {
        return $this->modelo->count();
    }

    /**
     *  Method orderBY ORM 
    */

    public function orderBy($field, $ascending = 'ASC') // ASC or DESC
    {
        return $this->modelo->orderBy($field, $ascending);
    }

    /**
     *  Method all ORM 
    */

    public function all()
    {
        return $this->modelo->get()->toArray();
    }

    /**
     *  Method create ORM 
    */

    public function createUser($UserData): User
    {
        return User::create($UserData);
    }

    /**
     *  Method find ORM 
    */

    public function getUser($user): User
    {
        return User::findOrFail($user);
    }

    public function getAllClients()
    {
        return User::role('Client')->get();
    }

    /**
     *  Method where ORM, get user for email
    */

    public function checkEmail($email): User | null
    {
        return User::where('email', '=', $email)->first();
    }

    /**
     *  Method where ORM, get user for user_id_parent and user_id
    */

    public function belongsToUser($user_id, $auth_user)
    {
        return User::where('user_id', '=', $user_id)->where('user_id_parent', '=', $auth_user)->count();
    }

    /**
     *  Method update ORM
    */

    public function updateUser($user, $UserData)
    {
        $user = $this->getUser($user);

        $user->fill($UserData);

        if ($user->isClean()) {
            return 'FAILCHANGEFALSE';
        }

        $user->update();

        return $user;
    }

    /**
     *  Method delete ORM
    */

    public function deleteUsers($field, $value)
    {
        User::where($field, '=', $value)->delete();
    }

    /**
     *  Method update ORM, changed user status false
    */

    public function disableUsers($field, $value)
    {
        User::where($field, '=', $value)->update(['status' => false]);
    }
}