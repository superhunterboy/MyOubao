<?php

/**
 * 活动类型表
 *
 * @author frank
 */
class PromoType extends BaseModel {

    protected $table = 'promo_types';
    public static $resourceName = 'PromoType';
    public static $columnForList = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'name'
    ];
    public $orderColumns = [
        'name' => 'asc',
    ];
    public static $rules = [
        'name' => 'required|max:30',
    ];

}
