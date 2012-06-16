<?php
/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 * note: Some functionality were taken from KohanaPHP Auth library
 *
 * @license     MIT Licence
 * @category    Libraries
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @version     1.0.0
 */
class Strong_Driver_Activerecord extends Strong_Driver
{
    /**
     * User login check based on driver
     * 
     * @return booleon
     */
    public function loggedIn() {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    /**
     * To authenticate user based on username or email
     * and password
     * 
     * @param string $usernameOrEmail 
     * @param string $password 
     * @return booleon
     */
    public function login($usernameOrEmail, $password) {
        if(! is_object($usernameOrEmail)) {
            $user = User::find_by_username_or_email($usernameOrEmail, $usernameOrEmail);
        }

        if(($user->email === $usernameOrEmail || $user->username === $usernameOrEmail) && $user->password === $password) {
            return $this->completeLogin($user);
        }

        return FALSE;
    }

    /**
     * Login and store user details in Session
     * 
     * @param array $user 
     * @return booleon
     */
    protected function completeLogin($user) {
        $users = User::find($user->id);
        $users->logins = $user->logins + 1;
        $users->last_login = time();
        $users->save();

        $userInfo = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'logged_in' => TRUE
        );

        return parent::completeLogin($userInfo);
    }
}
