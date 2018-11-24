<?php

class  SecurityQuestion extends BaseModel {
    protected $table = 'security_questions';
    protected $fillable = [
        'user_id',
        'question_id',
        'content',
    ];
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'user_id',
        'question_id',
        'content',
    ];
    
    public static $rules = [
        'user_id' => 'required',
        'question_id' => 'required',
        'content' => 'required',
    ];
    
    public static function getAllQuestions(){
        return self::get();
    }
    
    public static function getContentById($id){
        return self::where('id', $id)->first();
    }
    
    public static function getQuestionById($id){
        if(empty($id)){
            return false;
        }
        return self::where('id', $id)->first();
    }
    
    public static function getIdByContent($content){
        if(empty($content)){
            return false;
        }
        return self::where('content', $content)->first();
    }
}
