<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'personale';
    }

    public function rules()
    {
        return [
            [['nome', 'cognome', 'username', 'password', 'email', 'ruolo'], 'required'],
            ['username', 'unique'],
            ['email', 'email'],

            [['approvazione', 'blocco','is_totp_enabled'], 'boolean'],
            ['tentativi', 'integer'],

            [['partita_iva', 'recapito_telefonico', 'azienda','token','totp_secret'], 'string'],

            [
                'immagine',
                'file',
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'skipOnEmpty' => true
            ],

            // token può essere vuoto finché non viene generato
            ['token', 'string'],
        ];
    }

    /* ============================================================
     *  LOGIN & AUTENTICAZIONE
     * ============================================================ */

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // La colonna si chiama "token", quindi usiamo quella
        return static::findOne(['token' => $token]);
    }

    public static function findByUsername($value)
    {
        return static::find()
            ->where(['username' => $value])
            ->orWhere(['email' => $value])
            ->one();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /* ============================================================
     *  TOKEN RESET PASSWORD
     * ============================================================ */

  public function generatePasswordResetToken()
{
   return $this->token = Yii::$app->security->generateRandomString() . '_' . time();
}


    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(['token' => $token]);
    }

  public static function isPasswordResetTokenValid($token)
{
    if (empty($token)) {
        return false;
    }

    $timestamp = (int) substr($token, strrpos($token, '_') + 1);
    $expire = 1200; // 20  minuti

    return $timestamp + $expire >= time();
}


    /* ============================================================
     *  METODI UTILI
     * ============================================================ */

    public function isApproved()
    {
        return (bool) $this->approvazione;
    }
}
