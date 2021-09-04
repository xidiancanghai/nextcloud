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


class LoginIp {


    private $table = "login_ip";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

    public function Update(string $uid, string $ip) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $id = $this->Exists($uid, $ip);
        if ($id != 0){
            return ;
        } else {
            $query->insert($this->table)
				->values([
                    'uid' => $query->createNamedParameter($uid),
					'ip' => $query->createNamedParameter($ip),
                    'update_time' => $query->createNamedParameter(time())
				]);
			$result = $query->execute();
        }
		
    }

    public function Exists(string $uid, string $ip) {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('id')->from($this->table)->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)))->andWhere($qb->expr()->eq('ip', $qb->createNamedParameter($ip)))
            ->andWhere($qb->expr()->lt('update_time', $qb->createNamedParameter(time()-600)));
        $result = $qb->execute();
        $id = $result->fetch();
        $result->closeCursor();
        return (int)$id;
    }

    public function GetLog(string $uid, int $page, int $limit) {
        $offset = $page * $limit;
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
                'time' => $rows['update_time'],
            ));
		}
        $result->closeCursor();
        json_encode("list = ". json_encode($result));
        return $list;
    }
}

