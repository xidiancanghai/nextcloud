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


class FileLevel {


    private $table = "file_level";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

    public function Update(string $uid, string $path, string $level) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $id = $this->Exists($uid, $path);
        if ($id != 0){
            $query->update($this->table)->set('level', $query->createNamedParameter($level))
            ->set('update_time',$query->createNamedParameter(time()))
            ->where($query->expr()->eq('id', $query->createNamedParameter($id)));
		    $query->execute();
        } else {
            $query->insert($this->table)
				->values([
                    'uid' => $query->createNamedParameter($uid),
					'path' => $query->createNamedParameter($path),
                    'level' => $query->createNamedParameter($level),
                    'update_time' => $query->createNamedParameter(time())
				]);

			$result = $query->execute();
        }
		
    }

    public function Exists(string $uid, string $path) {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('id')->from($this->table)->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)))->andWhere($qb->expr()->eq('path', $qb->createNamedParameter($path)));
        $result = $qb->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return (int)$row['id'];
    }

}

