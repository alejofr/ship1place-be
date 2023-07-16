<?php

namespace App\Services;

use App\Models\Province;
use App\Repositories\ProvinceRepository;

class ProvinceService{

    /**
     *  The provinceRepository for consuming the repository Province
    */

    private $provinceRepository;

    public $fields = [
        'provinces.province_id',
        'provinces.name',
        'provinces.code',
        'provinces.country_id',
        'countries.name AS country_name',
        'countries.code AS country_code',
        'countries.phone_code AS country_phone_code'
    ];

    /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(ProvinceRepository $provinceRepository)
    {
       $this->provinceRepository =  $provinceRepository;
    }

     /**
      * Return an especify Province
      *
      * @return  App\Models\Province
    */

    public function showPronvince($id) : Province
    {
        return $this->provinceRepository->getProvince($id);
    }

    /**
     * Return Provinces LIST.
     *
     * @return array
    */

    public function index($limit, $page, $query = null, $orderBy = null, $ascending = null)
    {
        $this->provinceRepository->province($this->fields);
        $this->provinceRepository->leftJoinCountry();

        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;
        
        if( $query != null ){
            $this->provinceRepository->query($query);
        }

        $count = $this->provinceRepository->count();
        $this->provinceRepository->limit($limit, $page);

        if( $orderBy != null){
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $this->provinceRepository->orderBy($orderBy,  $direction);
        }
        
        $results = $this->provinceRepository->all();

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
     * Return Provinces All Search.
     *
     * @return array
    */

    public function searchProvince($query = null, $country_id = null)
    {
        $results = [];
        $this->provinceRepository->province([$this->fields[0], $this->fields[1], $this->fields[2]]);
        $this->provinceRepository->leftJoinCountry();

        
        if( $country_id != null ){
            $this->provinceRepository->queryIdCountry($country_id);
        }
        if( $query != null ){
            $this->provinceRepository->query($query);
        }

            
        $results = $this->provinceRepository->all();
        

        return ['data' => $results];
    }

    /**
     * Create an instance of Province
     * 
     * @return  App\Models\Province
    */

    public function storeProvince($dataProvince) : Province
    {
        return $this->provinceRepository->createProvince($dataProvince);
    }

    /**
     * Is check name province
     * 
     * @return bool
    */

    public function isNameProvince($value) : bool
    {
        return $this->provinceRepository->isCheckValue('name', $value) == null ? false : true;
    }

    /**
     * Is check code province
     * 
     * @return bool
    */

    public function isCodeProvince($value) : bool
    {
        return $this->provinceRepository->isCheckValue('code', $value) == null ? false : true;
    }

    /**
     * Is check code country and province are eqal
     * 
     * @return bool
    */

    public function checkIdCountryAndIdProvince($country_id, $province_id) : bool
    {
        return $this->provinceRepository->compareIdCountryAndIdProvince($country_id, $province_id) == null ? false : true;
    }
}
