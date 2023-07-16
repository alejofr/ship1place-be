<?php

namespace App\Services;

use App\Models\Country;
use App\Repositories\CountryRepository;

class CountryService{

    /**
     *  The countryRepository for consuming the repository Country
    */

    private $countryRepository;

    /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(CountryRepository $countryRepository)
    {
       $this->countryRepository =  $countryRepository;
    }


    /**
      * Return an especify Country
      *
      * @return  App\Models\Country
    */

    public function showCountry($id) : Country
    {
        return $this->countryRepository->getCountry($id);
    }

    /**
     * Return Countries LIST.
     *
     * @return array
    */

    public function index($limit, $page, $query = null, $orderBy = null, $ascending = null)
    {
        $this->countryRepository->country();
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;
        
        if( $query != null ){
            $this->countryRepository->query($query);
        }

        

        $count = $this->countryRepository->count();
        $this->countryRepository->limit($limit, $page);

        if( $orderBy != null){
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $this->countryRepository->orderBy($orderBy,  $direction);
        }

        $results = $this->countryRepository->all();

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
     * Return Countries Search All.
     *
     * @return array
    */

    public function searchContry($query = null)
    {
        $results = [];
        $this->countryRepository->country();

        if( $query != null ){
            $this->countryRepository->query($query);
            $results = $this->countryRepository->all();
        }

        return ['data' => $results];
    }

    /**
     * Create an instance of Country
     * 
     * @return  App\Models\Country
    */

    public function storeCountry($dataCountry) : Country
    {
        return $this->countryRepository->createCountry($dataCountry);
    }
    

    /**
     * Is check name
     * 
     * @return  bool
    */

    public function isNameCountry($value) : bool
    {
        return $this->countryRepository->isCheckValue('name', $value) == null ? false : true;
    }

    /**
     * Is check code
     * 
     * @return  bool
    */

    public function isCodeCountry($value) : bool
    {
        return $this->countryRepository->isCheckValue('code', $value) == null ? false : true;
    }
}
