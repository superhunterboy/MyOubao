<?php

class BlackjackJacpotDetails extends BaseModel{
    protected $table = 'casino_jacpot_details';
    protected $jacpotPrize = NULL;
    static $cacheLevel = self::CACHE_LEVEL_FIRST;


    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'man_project_id',
        'user_id',
        'request_table_id',
        'jacpot_id',
        'lottery_id',
        'request_prize',
        'available_prize',
        'balance',
        'created_at',


    ];
    public static $listColumnMaps = [

    ];

    protected $fillable = [
        'man_project_id',
        'user_id',
        'request_table_id',
        'jacpot_id',
        'lottery_id',
        'request_prize',
        'available_prize',
        'balance',
        'created_at',
        'updated_at'
    ];

    public static $rules = [

    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

     function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public function createDetails(){
        $result =$this->save(self::$rules);
        return $result;
    }

    static function getJackpotDetailByManProjectId($p_pid,$columns=['*']){
        $data = self::where('man_project_id',$p_pid)->get($columns)->first();
        return $data;
    }


}