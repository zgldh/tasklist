<?php
/**
 * Procss_log 处理模型<br />
 * 用于记录任务执行的日志。<br />
 * @author zgldh
 *
 */
class Process_log_model extends MY_Model
{
    const TABLE = 'process_log';
    public function getByPK($process_id)
    {
        if ($this->cache_pk->hasData ( $process_id ))
        {
            return $this->cache_pk->getData ( $process_id );
        }
        
        $raw = $this->db->get_where ( self::TABLE, array (ProcessLogPeer::PK => $process_id ) )->row_array ();
        $process = $raw ? new ProcessLogPeer ( $raw ) : false;
        
        $this->cache_pk->setData ( $process_id, $process );
        
        return $process;
    }
    /**
     * 更新数据 或 插入数据
     *
     * @param ProcessLogPeer $peer            
     */
    public function save(& $peer)
    {
        return parent::base_save ( self::TABLE, $peer );
    }
    /*
     * (non-PHPdoc) @see MY_Model::columns()
     */
    protected function columns()
    {
        return array(
                'log_id',
                'task_id',
                'log_content',
                'log_date'
                );
    }
}
/**
 * @property int $log_id = 0 日志id
 * @property int $task_id = 0 任务id
 * @property string $log_content = '' 日志内容
 * @property string $log_date = '' 日志时间戳
 * @author zgldh
 *
 */
class ProcessLogPeer extends BasePeer
{
    const PK = 'log_id';
 
    function __construct($raw = null)
    {
        parent::__construct ( $raw, __CLASS__ );
    }
    public function getPrimaryKeyName()
    {
        return self::PK;
    }
    public function getPrimaryKeyValue()
    {
        return $this->log_id;
    }
    public function save()
    {
        ProcessLogPeer::model ()->save ( $this );
    }
    /**
     *
     * @return Process_log_Model
     */
    public static function model()
    {
        $CI = & get_instance ();
        return $CI->process_log_model;
    }
    
    /**
     * 记录一个日志， 自动保存
     * 
     * @param int $task_id
     *            任务id
     * @param string $content
     *            日志内容
     */
    public static function log($task_id, $content)
    {
        $log = new ProcessLogPeer ();
        $log->task_id = $task_id;
        $log->log_content = $content;
        $log->log_date = self::getTimeStamp ();
        $log->save ();
        return $log;
    }
}

?>