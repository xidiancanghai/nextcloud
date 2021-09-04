<?php
namespace OC\User;

use OC\Cache\CappedMemoryCache;
use OCP\IDBConnection;
use OCP\User\Backend\ABackend;
use OCP\User\Backend\ICheckPasswordBackend;
use OCP\User\Backend\ICountUsersBackend;
use OCP\User\Backend\ICreateUserBackend;
use OCP\User\Backend\IGetDisplayNameBackend;
use OCP\User\Backend\IGetHomeBackend;
use OCP\User\Backend\IGetRealUIDBackend;
use OCP\User\Backend\ISetDisplayNameBackend;
use OCP\User\Backend\ISetPasswordBackend;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


class SysLogInfo {


    private $table = "syslog_info";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

   
    public function Insert(string $uid, string $log, string $ip) {

        if ($uid == null || $uid === '') {
            return;
        }
        if ($log == null || $log === '') {
            return;
        }
        if ($ip == null || $ip === '') {
            return;
        }

        $this->fixDI();

        $query = $this->dbConn->getQueryBuilder();
        $query->insert($this->table)
        ->values([
            'uid' => $query->createNamedParameter($uid),
            'log' => $query->createNamedParameter($log),
            'ip' => $query->createNamedParameter($ip),
            'create_time' => $query->createNamedParameter(time())
        ]);
        $query->execute();
    } 

    public function GetLog(string $uid, int $offset, int $limit) {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();

        if ($uid == '') {
            $qb->select('*')->from($this->table)->orderBy('id', 'DESC');
        } else {
            $qb->select('*')->from($this->table)->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)))->orderBy('id', 'DESC');
        }
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $result = $qb->execute();
        $list = array();

        while ($rows = $result->fetch()) {
            array_push($list, array(
                'id' => $rows['id'],
                'uid' => $rows['uid'],
                'ip' => $rows['ip'],
                'log' => $rows['log'],
                'time' => $rows['create_time'],
            ));
		}
        $result->closeCursor();
        json_encode("list = ". json_encode($result));
        return $list;

    }

}

