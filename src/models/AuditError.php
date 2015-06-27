<?php

namespace bedezign\yii2\audit\models;

use bedezign\yii2\audit\components\db\ActiveRecord;
use bedezign\yii2\audit\components\Helper;

/**
 * Class AuditError
 * @package bedezign\yii2\audit\models
 *
 * @property int           $id
 * @property int           $entry_id
 * @property string        $created
 * @property string        $message
 * @property int           $code
 * @property string        $file
 * @property int           $line
 * @property mixed         $trace
 * @property int           $status
 *
 * @property AuditEntry    $entry
 */
class AuditError extends ActiveRecord
{
    /**
     * @var array
     */
    protected $serializeAttributes = ['trace'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audit_error}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntry()
    {
        return $this->hasOne(AuditEntry::className(), ['id' => 'entry_id']);
    }

    /**
     * @param AuditEntry $entry
     * @param            $exception
     * @return null|static
     */
    public static function log(AuditEntry $entry, $exception)
    {
        $error = new static();
        $error->entry_id = $entry->id;
        $error->record($exception);
        return $error->save(false) ? $error : null;
    }

    /**
     * @param AuditEntry $entry
     * @param            $message
     * @param int        $code
     * @param string     $file
     * @param int        $line
     * @param array      $trace
     * @return null|static
     */
    public static function logMessage(AuditEntry $entry, $message, $code = 0, $file = '', $line = 0, $trace = [])
    {
        $error = new static();
        $error->entry_id = $entry->id;
        $error->message = $message;
        $error->code = $code;
        $error->file = $file;
        $error->line = $line;
        $error->trace = Helper::cleanupTrace($trace);
        return $error->save(false) ? $error : null;
    }

    /**
     * @param \Exception $exception
     */
    public function record(\Exception $exception)
    {
        $this->message = $exception->getMessage();
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = Helper::cleanupTrace($exception->getTrace());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Error Code'
        ];
    }


}
