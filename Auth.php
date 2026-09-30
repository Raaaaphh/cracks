<?php

/**
 * Description of Auth
 *
 * @author 
 */
class Auth {
    use tSingleton;
    const COOKIENAME = 'authbypass';
    
    protected function __construct(){
        session_start();
        if(isset($_COOKIE[self::COOKIENAME])) {
            $this->log($_COOKIE[self::COOKIENAME]);
        }
    }
    
    public function subscribe($login, $pwd): bool {
        global $db;
        $q = 'insert into users(login, pwd, isadmin) values(:login, :pwd, 0)';
        $stmt = $db->prepare($q);
        $stmt->bindParam(':login', $login);
        $pwdHash = hash('sha256', $pwd);
        $stmt->bindParam(':pwd', $pwdHash);
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $exception) {
            $query = 'select * from users where login="'.$login.'"';
            $ls = $db->query($query, PDO::FETCH_ASSOC);
            if(!empty($ls)) {
                return false;
            }
            throw $exception;
        }
    }
    
    public function tryLog($login, $pwd): bool {
        global $db;
        $q = 'select * from users where login="'.$login.'" and pwd="'.md5($pwd).'"';
        $found = null;
        $ls = $db->query($q, PDO::FETCH_ASSOC);
        if(!empty($ls)) {
            foreach($ls as $l) { $found = $l; }
        }
        if($found) {
            $this->log($found['id']);
            return true;
        } else {
            return false;
        }
    }
    
    public function log($id) {
        $_SESSION['userid'] = $id;
    }
    
    public function logoff() {
        $_SESSION['userid'] = null;
    }
    
    public function isLogged() {
        return !empty($_SESSION['userid']);
    }
    
    public function getSid() {
        return session_id();
    }
    
    public function getCodeFromLogin($login) {
        global $db;
        $q = 'select id, pwd from users where login="'.$login.'"';
        return $db->query($q)->fetch(PDO::FETCH_ASSOC);
    }
    
    public function resetPwd($id, $code, $newPwd) {
        global $db;
        $q = 'update users set pwd="'.md5($newPwd).'" where id="'.$id.'" and pwd="'.$code.'"';
        $db->query($q);
    }
}
