<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Modello per la tabella error_log
 */
class ErrorLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%error_log}}';
    }

    public function rules()
    {
        return [
            [['type', 'message'], 'required'],
            [['message', 'trace'], 'string'],
            [['code', 'line', 'user_id', 'status_code'], 'integer'],
            [['type', 'file', 'url', 'user_ip', 'request_method'], 'string', 'max' => 500],
            [['is_handled'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Tipo',
            'message' => 'Messaggio',
            'code' => 'Codice',
            'file' => 'File',
            'line' => 'Linea',
            'trace' => 'Trace',
            'url' => 'URL',
            'user_id' => 'Utente',
            'user_ip' => 'IP',
            'request_method' => 'Metodo',
            'status_code' => 'Stato HTTP',
            'is_handled' => 'Gestito',
            'created_at' => 'Data',
        ];
    }

    /**
     * Salva un'eccezione nel database
     */
    public static function logException($exception)
    {
        try {
            $model = new self();
            $model->type = get_class($exception);
            $model->message = $exception->getMessage();
            $model->code = $exception->getCode();
            $model->file = $exception->getFile();
            $model->line = $exception->getLine();
            $model->trace = $exception->getTraceAsString();

            // Dati richiesta
            $request = Yii::$app->request;
            if ($request instanceof \yii\web\Request) {
                $model->url = $request->get();
                $model->user_ip = $request->getUrl()->toStringUserIP();
                $model->request_method = $request->getMethod();
            }

            // Utente
            if (!Yii::$app->user->isGuest) {
                $model->user_id = Yii::$app->user->id;
            }

            if (!$model->save()) {
                Yii::error('Errore salvataggio error_log: ' . print_r($model->errors, true));
            }

            return $model;
        } catch (\Exception $e) {
            Yii::error('Errore in logException: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Salva un errore PHP nel database
     */
    public static function logPhpError($code, $message, $file, $line)
    {
        try {
            $errorTypes = [
                E_ERROR => 'E_ERROR',
                E_WARNING => 'E_WARNING',
                E_PARSE => 'E_PARSE',
                E_NOTICE => 'E_NOTICE',
                E_CORE_ERROR => 'E_CORE_ERROR',
                E_CORE_WARNING => 'E_CORE_WARNING',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                E_USER_ERROR => 'E_USER_ERROR',
                E_USER_WARNING => 'E_USER_WARNING',
                E_USER_NOTICE => 'E_USER_NOTICE',
                E_STRICT => 'E_STRICT',
                E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                E_DEPRECATED => 'E_DEPRECATED',
                E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            ];

            $model = new self();
            $model->type = $errorTypes[$code] ?? 'E_UNKNOWN';
            $model->message = $message;
            $model->code = $code;
            $model->file = $file;
            $model->line = $line;

            $request = Yii::$app->request;
            if ($request instanceof \yii\web\Request) {
                $model->url = $request->getUrl()->toString();
                $model->user_ip = $request->getUserIP();
                $model->request_method = $request->getMethod();
            }

            if (!Yii::$app->user->isGuest) {
                $model->user_id = Yii::$app->user->id;
            }

            if (!$model->save()) {
                Yii::error('Errore salvataggio error_log: ' . print_r($model->errors, true));
            }

            return $model;
        } catch (\Exception $e) {
            Yii::error('Errore in logPhpError: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Salva errore fatale
     */
    public static function logFatalError()
    {
        try {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                self::logPhpError($error['type'], $error['message'], $error['file'], $error['line']);
            }
        } catch (\Exception $e) {
            Yii::error('Errore in logFatalError: ' . $e->getMessage());
        }
    }

    /**
     * Errori non gestiti
     */
    public static function getUnhandled($limit = 50)
    {
        return self::find()
            ->where(['is_handled' => false])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Errori recenti
     */
    public static function getRecent($days = 7)
    {
        return self::find()
            ->where(['>=', 'created_at', date('Y-m-d H:i:s', strtotime("-{$days} days"))])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    /**
     * Contrassegna come gestito
     */
    public function markHandled()
    {
        $this->is_handled = true;
        return $this->save(false);
    }

    /**
     * Ritorna nome file breve
     */
    public function getShortFile()
    {
        if (empty($this->file)) {
            return 'N/A';
        }
        return basename($this->file) . ':' . ($this->line ?? '');
    }
}