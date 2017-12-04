<?php

namespace App;
/**
 * User: idgu
 * Date: 19.11.2017
 * Time: 18:34
 */
class Config
{

    /**
     * Config mail
     */
    const MAIL_HOST = 'smtp.gmail.com';
    const MAIL_USERNAME = '';
    const MAIL_PASSWORD = '';
    const MAIL_SMTP_SECURE = 'ssl';
    const MAIL_PORT = 465;


    const DB_HOST = 'localhost';
    const DB_NAME = 'mvc';
    const DB_USER = 'root';
    const DB_PASSWORD = '';

    /**
     * true - show errors in browser
     *
     * false - save errors in logs, and display error messages
     */
    const SHOW_ERRORS = true;
    const URL = 'http://localhost/mvc-ideas/public';

    /**
     * Key used to create token hash in \App\Token
     */
    const SECRET_KEY = '5enCAecoEjwXRVXdSi7MuWjLrPQI7Wr3';

}