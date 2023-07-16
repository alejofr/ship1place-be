<?php

namespace App\Services;

use App\Models\Package;
use App\Repositories\PackageRepository;

class PackageService{

    /**
     *  The packageRepository for consuming the repository Package
    */

    private $packageRepository;


    /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(PackageRepository $packageRepository)
    {
       $this->packageRepository =  $packageRepository;
    }

    /**
     * Return Packages LIST.
     *
     * @return array
    */

    public function index($limit, $page, $query = null, $orderBy = null, $ascending = null, $user_id = null)
    {
        $this->packageRepository->package();
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;

        if( $user_id != null){
            $this->packageRepository->queryRelation('user_id', $user_id);
        }
        
        if( $query != null ){
            $this->packageRepository->query($query);
        }


        $count = $this->packageRepository->count();
        $this->packageRepository->limit($limit, $page);

        if( $orderBy != null){
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $this->packageRepository->orderBy($orderBy,  $direction);
        }

        $results = $this->packageRepository->all();

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
     * Return Packages All Search.
     *
     * @return array
    */

    public function search($query = null, $user_id = null)
    {
        $this->packageRepository->package();
 

        if( $user_id != null){
            $this->packageRepository->queryRelation('user_id', $user_id);
        }
        
        if( $query != null ){
            $this->packageRepository->query($query);
        }


        $results = $this->packageRepository->all();

        return [
            'data' => $results,
        ];
    }

    /**
     * Create an instance of Package
     * 
     * @return  App\Models\Package
    */

    public function storePackage($dataPackage) : Package
    {
        if( isset($dataPackage['extra']) ){
            $dataPackage['extra'] = json_encode(json_decode($dataPackage['extra']));
        }
        
        return $this->packageRepository->createPackage($dataPackage);
    }

     /**
      * Return bool in check field name Package
      *
      * @return  bool
    */

    public function isCheckName($name, $userId) : bool
    {
        return $this->packageRepository->isCheckName($name, $userId);
    }

    
    /**
      * Return an especify Package
      *
      * @return  App\Models\Package
    */

    public function showPackage($id) : Package
    {
        return $this->packageRepository->getPackage($id);
    }
}