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


class FileType {


    private $table = "file_type";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

    public function Update(string $fileTypes) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $id = $this->Exists();
        if ($id != 0){
           $query->update($this->table)->set('file_types',$query->createNamedParameter($fileTypes));
           $query->execute();
        } else {
            $query->insert($this->table)
				->values([
                    'file_types' => $query->createNamedParameter($fileTypes)
				]);
			$result = $query->execute();
        }	
    }

    public function Exists() {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $query->select('*')->from($this->table);
        $result = $query->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return (int)$row['id'];
    }
  
    public function GetFileTypes() {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $query->select('*')->from($this->table);
        $result = $query->execute();
        $row = $result->fetch();
        $result->closeCursor();
        if ($row == false) {
            return array();
        }
        $fileTypes = (string)$row['file_types'];
        return explode(',', $fileTypes);
    }
}

