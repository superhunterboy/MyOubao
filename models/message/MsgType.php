<?php
class MsgType extends BaseModel {
    protected $table = 'msg_types';

    protected $fillable = [
        'name',
        'description',
        'for_admin',
        'functionality_id',
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'updated_at' => 'desc'
    ];
    public static $titleColumn = 'name';

    public static $resourceName = 'MsgType';
    public static $mainParamColumn = 'for_admin';
    public static $columnForList = [
        'name',
        'description',
        'for_admin',
        'updated_at',
    ];
    public static $rules = [
        'name'             => 'required|between:3,20',
        'description'      => 'required|between:3,255',
        'for_admin'        => 'in:0,1',
        'functionality_id' => 'integer',
    ];

    public function msg_messages()
    {
        return $this->hasMany('MsgMessage');
    }

    public static function getMsgTypesByGroup($iGroup = null)
    {
        $aColumns = ['id', 'name', 'for_admin'];
        if ($iGroup === null) $aMsgTypes = self::all($aColumns);
        else $aMsgTypes = self::where('for_admin', '=', $iGroup)->get($aColumns);
        $data = [];
        foreach ($aMsgTypes as $key => $value) {
            $data[$value->id] = $value->name;
        }
        return $data;
    }
}