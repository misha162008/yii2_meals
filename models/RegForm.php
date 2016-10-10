<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;


/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RegForm extends Model
{
    public $username;
    public $password;
    public $email;
    public $phone;
    public $address;
    public $captcha;
 
    private $_user = true;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'email', 'password', 'phone', 'address'], 'required'],
             [['username', 'email'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
             ['email', 'email'],
             'password' => [['password'], 'string', 'min' => 4, 'max' => 60],
             ['username', 'string', 'length' => [4, 24]],
             ['address', 'string', 'length' => [4, 24]],
             ['phone', 'number'],
             ['captcha', 'required'],
             ['captcha', 'captcha'],

            // username is validated by validatePassword()
            ['username', 'validateUsername'],
            ['email', 'validateEmail'],
        ]; 
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUsername($attribute, $params)
    {

        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user !== false) {
                $this->addError($attribute, 'Такое имя уже существует');
            }
        }
    }

    public function validateEmail($attribute, $params)
    {

        if (!$this->hasErrors()) {
            $user = $this->getUserEmail();

            if ($user !== false) {
                $this->addError($attribute, 'Такой mail уже существует');
            }
        }
    }

    // /**
    //  * Logs in a user using the provided username and password.
    //  * @return boolean whether the user is logged in successfully
    //  */
    // public function login()
    // {
    //     if ($this->validate()) {
    //         return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
    //     }
    //     return false;
    // }

    // /**
    //  * Finds user by [[username]]
    //  *
    //  * @return User|null 
    //  */
    public function getUser()
    {
        if ($this->_user === true) {
            $this->_user = User::findByUsername($this->username);
            if ($this->_user === NULL) {
               return $this->_user = false;
            }
        }

        return $this->_user = true;
    }

    public function getUserEmail()
    {
            $this->_user = User::getUserEmail($this->email);
            if ($this->_user === NULL) {
               return $this->_user = false;
            }
        

        return $this->_user = true;
    }
}
