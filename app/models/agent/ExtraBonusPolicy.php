<?php

class ExtraBonusPolicy extends BaseModel {
        protected $table            = 'extra_bonus_policy';
        protected $softDelete       = false;
        protected $fillable         = [
                                    'id',
                                    'bonus_rules_id',
                                    'loss',
                                    'rate'
                                    ];
       
        public static $rules = [
                                'rate'     => 'required|numeric|min:0.01|max:0.99',
                                'loss'     => 'required|numeric|min:0.1',
                                ];
         protected function getRateFormattedAttribute() {
            return $this->attributes['rate'] * 100 . '%';
         }

        protected function getLossFormattedAttribute() {
            return $this->attributes['loss'] / 10000 . ' 万';
        }
         
        public function Bonuls_rule()
        {
            return $this->belongsTo('Bonus_rule');
        }
}
