<?php

namespace App\Services;

use App\Models\Template;
use App\Repositories\TemplateRepository;

class TemplateService{

    /**
     *  The templateRepository for consuming the repository Template
    */

    private $templateRepository;

    public $fields = [
        'templates.template_id',
        'templates.user_id',
        'templates.sender_id',
        'senders.name AS sender_name',
        'templates.receiver_id',
        'receivers.name AS receiver_name',
        'templates.name',
        'templates.pieces'
    ];

     /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(TemplateRepository $templateRepository)
    {
       $this->templateRepository =  $templateRepository;
    }

    /**
     * Return Templates LIST.
     *
     * @return array
    */

    public function index($limit, $page, $orderBy = null, $ascending = null, $user_id, $query = null)
    {
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;

        $this->templateRepository->template($this->fields);

        $this->templateRepository->leftJoin('Templates as senders', 'senders.Template_id', 'templates.sender_id');
        $this->templateRepository->leftJoin('Templates as receivers', 'receivers.Template_id', 'templates.receiver_id');

        $this->templateRepository->queryRelation('templates.user_id', $user_id);

        if( $query != null ){
            $this->templateRepository->query($query);
        }

        $count = $this->templateRepository->count();
        $this->templateRepository->limit($limit, $page);

        if( $orderBy != null){
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $orderBy = 'templates.'.$orderBy;
             $this->templateRepository->orderBy($orderBy,  $direction);
        }

        $results =  $this->templateRepository->all();

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
     * Create an instance of Template
     * 
     * @return  App\Models\Template
    */

    public function storeTemplate($data = [])
    {
        $data['pieces'] = json_encode($data['pieces']);

        return  $this->templateRepository->createTemplate($data);
    }

    /**
      * Return an especify Template
      *
      * @return  App\Models\Template
    */

    public function showTemplate($id) : Template
    {   
        return $this->templateRepository->getTemplate($id);
    }

}