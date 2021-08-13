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

class PasswordCycle {


    private $table = "password_cycle";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

    public function Update(string $uid, string $t) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        if ($this->Exists($uid)) {
            $query->update($this->table)->set('life', $query->createNamedParameter($t))
            ->where($query->expr()->eq('uid', $query->createNamedParameter($uid)));
		    $query->execute();
        } else {
            $query->insert($this->table)
				->values([
                    'uid' => $query->createNamedParameter($uid),
					'life' => $query->createNamedParameter($t)
				]);

			$result = $query->execute();
        }
		
    }

    public function Exists(string $uid) {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('life')->from($this->table)->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)));
        $result = $qb->execute();
        $row = $result->fetch();
        return $row !== false;
    }

    public function PassWordLife($uid) {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('life')->from($this->table)->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)));
        $result = $qb->execute();
        $row = $result->fetch();
        return $row['life'];
    }
   
}