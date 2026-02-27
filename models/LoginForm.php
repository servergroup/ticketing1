<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;   // username O email
    public $password;
    public $rememberMe = false;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();

        if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password)) {
            $this->addError($attribute, 'Credenziali errate.');
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            // username O email nello stesso campo
            $this->_user = User::find()
                ->where(['username' => $this->username])
                ->orWhere(['email' => $this->username])
                ->one();
        }

        return $this->_user;
    }

    public function login()
    {
        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        // BLOCCO
        if ($user->blocco || $user->tentativi <= 0) {
            $user->blocco = true;
            $user->save();
            return false;
        }

        // PASSWORD
        if (!Yii::$app->security->validatePassword($this->password, $user->password)) {

            $user->tentativi -= 1;

            if ($user->tentativi <= 0) {
                $user->tentativi = 0;
                $user->blocco = true;
            }

            $user->save();
            return false;
        }

        // RUOLI
        if (!in_array($user->ruolo, ['amministratore', 'itc', 'ict', 'sistemista', 'cliente', 'developer', 'personale'], true)) {
            return false;
        }

        // LOGIN OK
        return Yii::$app->user->login(
            $user,
            $this->rememberMe ? 3600 * 24 * 30 : 0
        );
    }

    public function bloccaTutto()
    {
        $user = $this->getUser();

        if (!$user) return;

        $user->tentativi = 0;
        $user->blocco = true;
        $user->save();
    }

    public function verifyBlocco($username)
    {
        return User::findOne(['username' => $username, 'blocco' => true]);
    }

    public function bloccaUser($username)
    {
        $user = User::findOne(['username' => $username]);
        if (!$user) return false;

        $user->blocco = true;
        return $user->save();
    }
}
