<?php
# 权限和角色的关联表
# 实际使用了roles表的rights字段来存储权限，functionality_role关联表没有使用, snowan comment on 2014-08-27
class FunctionalityRole extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'functionality_role';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
    protected $fillable = [];
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;

}