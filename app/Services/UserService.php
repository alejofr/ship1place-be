<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\StoreClientService;
use App\Services\User\StoreSubClientService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserService
{

    /**
     *  The userRepository for consuming the repository User 
    */

    private $userRepository;

    /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository =  $userRepository;
    }

    /**
     * Return Users LIST.
     *
     * @return array
    */

    public function index($limit, $page, $query = null, $country_id = null, $province_id = null, $city_id = null, $idClient = null, $orderBy = null, $ascending = null, $userType = null)
    {
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;

        $this->userRepository->user();

        if ($country_id != null) {
            $this->userRepository->queryRelation('country_id', $country_id);
        }

        if ($province_id != null) {
            $this->userRepository->queryRelation('province_id', $province_id);
        }

        if ($city_id != null) {
            $this->userRepository->queryRelation('city_id', $city_id);
        }

        if ($idClient != null) {
            $this->userRepository->queryRelation('user_id_parent', $idClient);
        }

        if ($query != null) {
            $this->userRepository->query($query);
        }

        $count = $this->userRepository->count();
        $this->userRepository->limit($limit, $page);

        if ($orderBy != null) {
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $this->userRepository->orderBy($orderBy,  $direction);
        }

        $results = $this->userRepository->all();

        for ($i = 0; $i < count($results); $i++) {
            $results[$i] = $this->cleanObjectUser($results[$i]);
        }

        return [
            'data' => $results,
            'pagination' => [
                'numPage' => intval($page),
                'resultPage' => count($results),
                'totalResult' => $count
            ]
        ];
    }

    /**
     * Return Object User.Removing the user_id_parent property and sending the parent's object user
     *
     * @return object
    */


    public function cleanObjectUser($user)
    {
        $user['user_parent'] = null;

        if ($user['user_id_parent'] != null) {
            $user['user_parent'] = $this->userRepository->getUser($user['user_id_parent']);
        }

        unset($user['user_id_parent']);

        return $user;
    }

     /**
     * Return Users Search LIST.
     *
     * @return array
    */

    public function searchUser($query = null, $country_id = null, $province_id = null, $city_id = null, $userType = null, $limit = null, $page = null, $orderBy = null, $ascending = null)
    {
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;

        $results = [];
        if ($userType != null) {
            $this->userRepository->userByType($userType);
        } else {
            $this->userRepository->user();
        }
        $count = $this->userRepository->count();
        $this->userRepository->limit($limit, $page);
        if ($orderBy != null) {
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $this->userRepository->orderBy($orderBy,  $direction);
        }
        $results = $this->userRepository->all();


        if ($query != null) {
            if ($country_id != null) {
                $this->userRepository->queryRelation('country_id', $country_id);
            }

            if ($province_id != null) {
                $this->userRepository->queryRelation('province_id', $province_id);
            }

            if ($city_id != null) {
                $this->userRepository->queryRelation('city_id', $city_id);
            }

            $this->userRepository->query($query);

            $count = $this->userRepository->count();
            $this->userRepository->limit($limit, $page);

            if ($orderBy != null) {
                $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
                $this->userRepository->orderBy($orderBy,  $direction);
            }

            $results = $this->userRepository->all();
        }
        for ($i = 0; $i < count($results); $i++) {
            $results[$i] = $this->cleanObjectUser($results[$i]);
        }

        return [
            'data' => $results,
            'pagination' => [
                'numPage' => intval($page),
                'resultPage' => count($results),
                'totalResult' => $count
            ]
        ];
    }

     /**
     * Return Users Clients LIST.
     *
     * @return array
    */

    public function getAllClients()
    {
        $user = $this->userRepository->getAllClients();

        return $user;
    }

    public function belongsToUser($user_id, $auth_user)
    {
        $user = $this->userRepository->belongsToUser($user_id, $auth_user);

        return $user;
    }

    /**
     * Create an instance of User
     * 
     * @return  App\Models\User
    */

    public function storeUser($type, $dataUser): User
    {
        $dataUser['password'] = $this->generate_bcrypt_password($dataUser['password']);

        if ($dataUser['consent_to_receive_newsletter']) {
            $dataUser['consent_date'] = Carbon::now();
        }

        switch ($type) {
            case 'sub-client':
                return StoreSubClientService::store($dataUser, $this->userRepository);
            default:
                return StoreClientService::store($dataUser, $this->userRepository);
        }
    }

    /**
      * Return an especify User
      *
      * @return  App\Models\User
    */

    public function showUser($id)
    {
        $user = $this->userRepository->getUser($id);

        return $user;
    }

    /**
      * Verify Email
      *
      * @return bool
    */

    public function isEmail($email): bool
    {
        return $this->userRepository->checkEmail($email) == null ? false : true;
    }

    public function getUserForEmail($email): User | null
    {
        return $this->userRepository->checkEmail($email);
    }

    public function generate_bcrypt_password($password)
    {
        return bcrypt($password);
    }

    public function loginDateTime($userData)
    {
        $user = $this->userRepository->updateUser($userData->user_id, [
            'last_login' => Carbon::now()
        ]);

        $userData->last_login = $user->last_login;
        $userData->getRoleNames()->first();

        return $userData;
    }

    /**
      * Delete user sub client
      *
      * @return void
    */

    public function deleteSubClients($user_id_parent)
    {
        $this->userRepository->deleteUsers('user_id_parent', $user_id_parent);
    }

    /**
      * Disable user sub client
      *
      * @return void
    */

    public function disableSubClients($user_id_parent)
    {
        $this->userRepository->disableUsers('user_id_parent', $user_id_parent);
    }

    /**
      * Generate password for user
      *
      * @return string
    */

    public function generatePassword()
    {
        return Str::random(10);
    }
}