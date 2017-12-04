<?php
/**
 * User: idgu
 * Date: 21.11.2017
 * Time: 23:44
 */

namespace App;

use App\Models\RememberedLogin;
use \App\Models\User;

class Auth
{

    /**
     * Regenerate session_id, create $_SESSION['username'] and if rember me is checkedd
     * add record to database and set cookie with remember_token
     *
     * @param $user
     * @param bool $remember_me
     */
    public static function login($user, $remember_me=false)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;

        if ($remember_me) {
            if($user->rememberLogin()) {
                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
            }
        }
    }


    /**
     * Log out user (Clear session, remember cookies)
     */
    public static function logout()
    {
        // Unset all of the session variables
        $_SESSION = [];

        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Finally destroy the session
        session_destroy();

        static::forgetLogin();
    }


    /**
     * If user try to visit page with insufficient permission, then you can use this metod to save in session page;
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['QUERY_STRING'];
    }


    /**
     *If earlier was invoke rememberRequestedPage(), then returns this page otherwise return  /
     *
     * @return string
     */
    public static function getReturnToPage()
    {
        return isset($_SESSION['return_to'])? '/'.$_SESSION['return_to'] : '/' ;
    }


    /**
     * Create user object when session or cookie exists
     * @return Models\obj|null
     */
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);
        } else {
            return static::loginFromRememberCookie();
        }
    }


    /**
     * Login user when cookie remember_me isset and is correct.
     *
     * @return mixed
     */
    protected static function loginFromRememberCookie()
    {
        $cookie = isset($_COOKIE['remember_me'])? $_COOKIE['remember_me'] : false;
        if ($cookie) {

            $remembered_login = RememberedLogin::findByToken($cookie);

            if ($remembered_login && ! $remembered_login->hasExpired()) {

                $user = $remembered_login->getUser();
                static::login($user, false);

                return $user;
            }
        }
    }


    /**
     * Clear remember database record and cookie
     */
    protected static function forgetLogin()
    {
        $cookie = isset($_COOKIE['remember_me'])? $_COOKIE['remember_me'] : false;

        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);

            if ($remembered_login) {
                $remembered_login->delete();
            }
            setcookie('remember_me', '', time()-3600);
        }
    }





    public static function saveTokenFrom($tokenValue)
    {
        $_SESSION['token_form'] = $tokenValue;
    }

    public static function checkTokenForm($token)
    {
        if (isset($_SESSION['token_form']) && $token == $_SESSION['token_form']) {
            unset($_SESSION['token_form']);
            return true;
        } else {
            return false;
        }
    }

}