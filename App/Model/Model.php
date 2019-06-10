<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/8
 * Time: 22:29
 */

namespace App\Model;


use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Mysqli\Mysqli;
use EasySwoole\Mysqli\TpORM;
use EasySwoole\MysqliPool\Mysql;

class Model extends TpORM
{
    protected $prefix;

    protected $softDelete = false;

    protected $throwable;

    protected $modelPath = '\\App\\Model';

    protected $createTime = true;

    protected $updateTime = true;

    protected $createTimeName = 'created_at';

    protected $updateTimeName = 'updated_at';

    public function __construct($data = null)
    {
        $this->prefix = Config::getInstance()->getConf( 'MYSQL.prefix' );
        $db = Mysql::defer('mysql');
        parent::__construct( $data );
        $this->setDb( $db );
    }

    public function add($data)
    {
        try{
            $date = date('Y-m-d H:i:s',time());

            if( $this->createTime === true )
            {
                $data[$this->createTimeName] = $date;
            }

            if ($this->updateTime === true)
            {
                $data[$this->updateTimeName] = $date;
            }

            return parent::insert( $data );
        } catch( \EasySwoole\Mysqli\Exceptions\ConnectFail $e ){
            $this->throwable = $e;
            return false;
        } catch( \EasySwoole\Mysqli\Exceptions\PrepareQueryFail $e ){
            $this->throwable = $e;
            return false;
        } catch( \Throwable $t ){
            $this->throwable = $t;
            return false;
        }
    }

    /**
     * @return array|bool|false|null
     */
    public function select()
    {
        try{
            return parent::select();
        } catch( \EasySwoole\Mysqli\Exceptions\ConnectFail $e ){
            $this->throwable = $e;
            return false;
        } catch( \EasySwoole\Mysqli\Exceptions\PrepareQueryFail $e ){
            $this->throwable = $e;
            return false;
        } catch( \Throwable $t ){
            $this->throwable = $t;
            return false;
        }
    }

    /**
     * @return array|bool
     */
    protected function find( $id = null )
    {
        try{
            if( $id ){
                return $this->byId( $id );
            } else{
                return parent::find();
            }
        } catch( \EasySwoole\Mysqli\Exceptions\ConnectFail $e ){
            $this->throwable = $e;
            return false;
        } catch( \EasySwoole\Mysqli\Exceptions\PrepareQueryFail $e ){
            $this->throwable = $e;
            return false;
        } catch( \Throwable $t ){
            $this->throwable = $t;
            return false;
        }
    }
}