<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 15-9-8
 * Time: 上午11:12
 */

class TransactionsRelatedUser  extends BaseModel {

    protected $table = 'transactions_related_users';
    protected $softDelete = false;
    public $timestamps = false;
    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'transaction_id',
        'related_user_id',
        'related_user_name',
    ];

    public static $resourceName = 'TransactionsRelatedUser';


}