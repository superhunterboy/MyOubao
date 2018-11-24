<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 计算总代升降点
 */
class CreateActivityReport extends BaseCommand {

    protected $sFileName = 'createactivityreport';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'activity:create-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create activity report';

    public function fire() {
        $aTabelInfo = Config::get('activity.reportTables');
        foreach ($aTabelInfo as $sTableName => $aTableInfo) {
            Schema::dropIfExists($sTableName);
            Schema::create($sTableName, function($table) {
                $table->increments('id');
            });
            $this->aFieldType = $aTableInfo['fieldType'];
            foreach ($aTableInfo['field'] as $sField) {
                $this->sField = $sField;
                Schema::table($sTableName, function($table) {
                    if (starts_with($this->aFieldType[$this->sField], 'string')) {
                        $aStringType = explode(':', $this->aFieldType[$this->sField]);
                        $table->string($this->sField, $aStringType[1])->nullable();
                    } else if (starts_with($this->aFieldType[$this->sField], 'timestamp')) {
                        $table->timestamp($this->sField)->nullable();;
                    } else if (starts_with($this->aFieldType[$this->sField], 'decimal')) {
                        $aDecimalType = explode(':', $this->aFieldType[$this->sField]);
                        $aDecimalValue = explode(',', $aDecimalType[1]);
                        $table->decimal($this->sField, $aDecimalValue[0], $aDecimalValue[1]);
                    } else if (starts_with($this->aFieldType[$this->sField], 'integer')) {
                        $table->integer($this->sField);
                    }
                });
            }
        }
    }

}
