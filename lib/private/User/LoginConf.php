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

    public function Update(int $t) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        if ($this->Exists()) {
            $query->update($this->table)->set('life', $query->createNamedParameter($t));
		    $query->execute();
        } else {
            $query->insert($this->table)
				->values([
					'life' => $query->createNamedParameter($t)
				]);

			$result = $query->execute();
        }
		
    }

    public function Exists() {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('life')->from($this->table);
        $result = $qb->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return $row !== false;
    }

    public function PassWordLife() {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('life')->from($this->table);
        $result = $qb->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return $row['life'];
    }
   
}