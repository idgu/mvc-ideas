<?php
/**
 * User: idgu
 * Date: 05.12.2017
 * Time: 11:28
 */

namespace App\Models;

use \App\Validator;
use \App\Token;
use PDO;

class Idea extends \core\Model
{


    private $errors = array();



    public function validate( Validator $validator)
    {
        $validator->validate();
        $this->errors = $validator->getErrors();
        return $validator->getErrors();
    }


    public function save(Validator $validator, $user_id, $notRequiredActivation = false)
    {
        $errors = $this->validate($validator);

        if (empty($errors)) {

            $token = new Token();
            $this->activation_hash = $token->getHash();

            if ($notRequiredActivation) {
                $sql = 'INSERT INTO ideas (user_id, categories_id, name, content, contribution, add_date, is_active) VALUES (:user_id, :categories_id, :name, :content, :contribution, :add_date, 1)';

            } else {
                $sql = 'INSERT INTO ideas (user_id, categories_id, name, content, contribution, add_date, activation_hash) VALUES (:user_id, :categories_id, :name, :content, :contribution, :add_date, :activation_hash)';

            }

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':categories_id', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':contribution', $this->contribution, PDO::PARAM_STR);
            $stmt->bindValue(':add_date', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);

            if (!$notRequiredActivation){
                $stmt->bindValue(':activation_hash', $this->activation_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }


    public function __construct($data = array())
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getCategory() {
        $sql = 'SELECT * FROM ideas_categories WHERE id = :categories_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue("categories_id", $this->categories_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }


    public function getErrors()
    {
        return $this->errors;
    }


    public static function getIdeasCount($active = false, $noActive = false)
    {
        $sql = 'SELECT id FROM ideas';
        if ($active) {
            $sql .= ' WHERE is_active = 1';
        }
        if ($noActive) {
            $sql .= ' WHERE is_active = 0';
        }
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }



    public static function getAll($orderOption = null, $active = false, $noactive = false)
    {
        $order = explode('/', $orderOption);

        $sql = 'SELECT i.*, ic.name AS category_name, u.name AS user_name, u.email AS user_email FROM ideas AS i, users AS u, ideas_categories AS ic WHERE i.user_id = u.id AND i.categories_id = ic.id';

        if ($active) {
            $sql .= ' AND i.is_active = 1';
        }

        if ($noactive) {
            $sql .= ' AND i.is_active = 0';
        }
        if (!empty($order) && $order) {
            if (isset($order[0])) {
                $sql .= ' ORDER BY '. $order[0];
            }
            if (isset($order[1])) {
                $sql .= ' '. strtoupper($order[1]);
            }
            if (isset($order[2])) {
                $sql .= ' LIMIT '. strtoupper($order[2]);
            }
        } else {
            $sql .= ' ORDER BY '. $orderOption;
        }


        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $ideas = $stmt->fetchAll();

        return $ideas;
    }



    public function addVote($user_id, $rate)
    {
        $sql = 'SELECT votes FROM ideas WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $record = $stmt->fetch()[0];
        var_dump($record);

        $sql = 'UPDATE ideas
                    SET votes = :votes
                    WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        if ($record) {
            $add = $record . '|'.$user_id.','.$rate;
        } else {
            $add = $user_id.','.$rate;
        }

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':votes', $add, PDO::PARAM_STR);
        $stmt->execute();

        $this->updateRanking();

    }


    public function updateVotes() {
        $sql = 'UPDATE ideas SET votes = :votes WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':votes', $this->votes, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $this->updateRanking();
    }


    private function updateRanking()
    {
        $refreshed_idea = Idea::get($this->id);
        $refreshed_idea->setRanking();

        $sql = 'UPDATE ideas SET ranking = :ranking WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':ranking', $refreshed_idea->ranking, PDO::PARAM_INT);
        $stmt->bindValue(':id', $refreshed_idea->id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    private function setRanking()
    {
        $votes = $this->getVotes();
        $ranking = 0;
        if ($votes) {
            foreach ($votes as $vote_rate) {
                $ranking += $vote_rate;
            }
        }
        $this->ranking = $ranking;
    }


    public function getVotes()
    {
        if ($this->votes) {
            $votes_db_array = explode('|', $this->votes);
            $votes_array = array();

            foreach ($votes_db_array as $value) {
                $temp_arr = explode(',', $value);
                $votes_array[$temp_arr[0]] = $temp_arr[1];
            }
            return $votes_array;
        }
        return 0;
    }



    public static function get($id)
    {
        $sql = 'SELECT i.*, ic.name AS category_name, u.name AS user_name, u.email AS user_email FROM ideas AS i, users AS u, ideas_categories AS ic WHERE i.id = :id AND i.user_id = u.id AND i.categories_id = ic.id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function searchIdea($name)
    {
        $sql = 'SELECT id, name FROM ideas WHERE name like :name and is_active = 1';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $name = '%'.$name.'%';
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();

    }



    public function update(Validator $validator, $data)
    {

        $this->name = $data['name'];
        $this->content = $data['content'];
        $this->contribution = $data['contribution'];
        $this->category = $data['category'];
        var_dump($data['category']);

        $errors = $this->validate($validator);

        if (empty($errors)) {
            $sql = 'UPDATE ideas
            SET name = :name, content = :content, contribution = :contribution, categories_id = :category  WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':contribution', $this->contribution, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public function accept()
    {
        $sql = 'UPDATE ideas SET is_active = 1 WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function delete()
    {

        if ($this->votes) {
            $votes_db_array = explode('|', $this->votes);
            $votes_array = array();

            $db = static::getDB();

            foreach ($votes_db_array as $value) {
                $temp_arr = explode(',', $value);
                $user = User::findById($temp_arr[0]);

                $user_voted = preg_replace("/$this->id,[0-9]+\|/", "",  $user->voted_ideas);
                $user_voted = preg_replace("/\|$this->id,[0-9]+/", "",  $user_voted);
                $user_voted = preg_replace("/$this->id,[0-5]+/", "",    $user_voted);
                $user->voted_ideas = $user_voted;
                $user->updateVotes();
            }
        }


        $sql = 'DELETE FROM ideas WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }


}