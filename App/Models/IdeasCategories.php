<?php
/**
 * User: idgu
 * Date: 05.12.2017
 * Time: 11:34
 */

namespace App\Models;

use PDO;

class IdeasCategories extends \core\Model
{

    public static function getAll()
    {
        $sql = 'SELECT * FROM ideas_categories';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getCategory()
    {

    }

}