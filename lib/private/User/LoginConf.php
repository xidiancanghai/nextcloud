<?php
declare(strict_types=1);

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

class LoginConf {


    private $table = "login_conf";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

    public function Update(int $retryTimes, int $interval) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $id = $this->GetId();
        if ($id != 0) {
            $query->update($this->table);
            if ($retryTimes != 0 ) {
                $query->set('retry_times', $query->createNamedParameter($retryTimes));
            }
            if ($interval != 0) {
                $query->set('interval', $query->createNamedParameter($interval));
            }
            $query->set('update_time', $query->createNamedParameter(time()));
            $query->where($query->expr()->eq('id', $query->createNamedParameter($id)));
		    $query->execute();
        } else {
            $t = time();
            $query->insert($this->table)
				->values([
					'retry_times' => $query->createNamedParameter($retryTimes),
                    'interval' => $query->createNamedParameter($interval),
                    'update_time' => $query->createNamedParameter($t),
                    'create_time' => $query->createNamedParameter($t),
				]);
			$query->execute();
        }
		
    }

    public function GetId() {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('id')->from($this->table);
        $result = $qb->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return (int)$row['id'];
    }

    public function GetConf() {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('retry_times','interval')->from($this->table);
        $result = $qb->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return array('retry_times' => (int)$row['retry_times'], 'interval' => (int)$row['interval']);
    }


}