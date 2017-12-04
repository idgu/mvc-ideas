<?php
/**
 * User: idgu
 * Date: 22.11.2017
 * Time: 07:21
 */

namespace App;


class Flash
{
    const SUCCESS = 'success';

    const INFO = 'info';

    const WARNING = 'warning';


    /**
     * Add message to waiting table
     *
     * @param $message
     * @param string $type
     */
    public static function addMessage($message, $type='success')
    {
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type
        ];
    }


    /**
     * If isset something in waiting room, then return this and clear, otherwise return null;
     *
     * @return array|null
     */
    public static function getMessages()
    {
        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset ($_SESSION['flash_notifications']);

            return $messages;
        }
    }

}