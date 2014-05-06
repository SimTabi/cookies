<?php


namespace Cookies;
class Cookies implements \SessionHandlerInterface {
    private $secret_key;
    private $keylen = 64; // Change the keylen to the length of strlen(hash($hashname, 'a'))
    private $name;
    public function __construct($secret_key) {
        $this->secret_key = $secret_key;

    }
    /* Used to set the name of the cookie only
     */ 
    public function open($save_path, $name) {
        $this->name = $name;
        return true;
    }
    

    /* We dont really care about the session_id.
     * The name of the cookie is set in the open() method.
     * You can only have one session / user anyway.
     * Returns the session data as a serialized string.
     */
    public function read($session_id) {
        $data = $_COOKIE[$this->name];
        if (strlen($data) < $this->keylen) {
            return "";
        }

        $hmac = substr($data, 0-$this->keylen);
        $session_data = substr($data, 0, 0-$this->keylen);
        if ($hmac !== hash_hmac('sha256', $session_data, $this->secret_key)) {
            return "";
        }
        return $session_data;

    }

    public function write($session_id, $session_data) {
        // At the moment only sha256 is supported. :)
        return setcookie($this->name, $session_data . hash_hmac('sha256', $session_data, $this->secret_key), 0, '/');
    }

    public function destroy($session_id) {
        return setcookie($this->name, '', -1, '/');
    }

    public function close() {
        return true;
    }
    public function gc($maxlifetime) {
        return true;
    }
}
