<?php
/**
 * User: idgu
 * Date: 22.11.2017
 * Time: 10:13
 */

namespace App\Models;

use \App\Token;
use PDO;

class RememberedLogin extends \Core\Model
{

    /**
     * Check if in remembered_logins exists $token, if does then
     * return instance of \Core\Model\RememberedLogin
     *
     * @param $token
     * @return mixed
     */
    public static function findByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins WHERE token_hash = :token_hash';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getUser()
    {
        return User::findById($this->user_id);
    }

    public function hasExpired()
    {
        return strtotime($this->expires_at) < time();
    }

    public function delete()
    {
        $sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);
        $stmt->execute();
    }


}