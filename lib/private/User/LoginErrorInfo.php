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

class LoginErrorInfo {


    private $table = "login_error_info";

    private $dbConn;

    public function __construct() {
	}

    private function fixDI() {
		if ($this->dbConn === null) {
			$this->dbConn = \OC::$server->getDatabaseConnection();
		}
	}

    public function AddError(string $uid) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $info = $this->GetInfo($uid);
        error_log(json_encode($info) . " " . $info['uid']);
        if ($info['uid'] != '') {
            $query->update($this->table);
            $query->set('times', $query->createNamedParameter($info['times']+1));
            $query->set('update_time', $query->createNamedParameter(time()));
            $query->where($query->expr()->eq('uid', $query->createNamedParameter($uid)));
		    $query->execute();
        } else {
            $t = time();
            $query->insert($this->table)
				->values([
					'uid' => $query->createNamedParameter($uid),
                    'times' => $query->createNamedParameter(1),
                    'update_time' => $query->createNamedParameter($t),
                    'create_time' => $query->createNamedParameter($t),
				]);
			$query->execute();
        }
		
    }

    public function Reset(string $uid) {
        $this->fixDI();
        $query = $this->dbConn->getQueryBuilder();
        $query->update($this->table);
        $query->set('times', $query->createNamedParameter(0));
        $query->set('update_time', $query->createNamedParameter(time()));
        $query->where($query->expr()->eq('uid', $query->createNamedParameter($uid)));
        $query->execute();
    }

    public function GetInfo($uid) {
        $this->fixDI();
        $qb = $this->dbConn->getQueryBuilder();
        $qb->select('uid','times','update_time')->from($this->table)->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)));
        $result = $qb->execute();
        $row = $result->fetch();
        $result->closeCursor();
        return array('uid' => (string)$row['uid'], 'times' => (int)$row['times'], 'update_time' => (int)$row['update_time']);
    }
   
    public function CanLogin(string $uid)  {
        $info = $this->GetInfo($uid);
        if ($info['uid'] == '') {
            return true;
        }
        $loginConf = new LoginConf();
        $conf = $loginConf->GetConf();

        if ($conf['retry_times'] == 0) {
            $conf['retry_times'] = 5;
        }

        if ($info['times'] < $conf['retry_times']) {
            return true;
        }

        if ($conf['interval'] == 0) {
            $conf['interval'] = 30;
        }

        if ( time() - $info['update_time'] > $conf['interval']) {
            return true;
        } 
        return false;
    }
   
}