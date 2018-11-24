<?php
class SecureCard extends BaseModel {
    protected $table = 'secure_cards';

    public static $titleColumn = 'number';

    const STATUS_ACTIVED = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_CHANGED = 3;

    public $validStatuses = [
        self::STATUS_ACTIVED => 'Actived',
        self::STATUS_INACTIVE => 'Inactived',
        self::STATUS_CHANGED => 'Changed',
    ];
    public $validate = [
        'number' => [
            'numeric' => [
                'rule' => ['numeric'],
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'safe_string' => [
            'between' => [
                'rule' => ['between',291,291],
            ],
        ],
        'status' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
    ];
}
