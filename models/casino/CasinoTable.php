<?php
/**
 * 彩票模型
 */
class CasinoTable extends Lottery {

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'CasinoTable';

    protected $table = 'casino_tables';

    /**
     * 返回数据列表
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getTitleList($bOrderByTitle = false){
        $aColumns = [ 'id', 'table_name'];
        $sOrderColumn = $bOrderByTitle ? 'table_name' : 'id';
        $oModels  = self::orderBy($sOrderColumn,'asc')->get($aColumns);
        $data     = [];
        foreach ($oModels as $oModel){
            $data[ $oModel->id ] = $oModel->table_name;
        }
        return $data;
    }

}

