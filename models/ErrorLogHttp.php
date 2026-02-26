<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ErrorLogHttp extends ActiveRecord
{
    public static function tableName()
    {
        return 'error_log';  // Senza prefisso
    }

    public function rules()
    {
        return [
            [['response_http', 'message'], 'required'],
            [['response_http'], 'string', 'max' => 10],
            [['message'], 'string'],
            [['id_cliente'], 'integer'],
            [['rotta'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'response_http' => 'Codice HTTP',
            'message' => 'Messaggio',
            'id_cliente' => 'Cliente',
            'rotta' => 'Rotta',
            'created_at' => 'Data',
        ];
    }

    /**
     * Salva errore HTTP - prima DB, poi JSON
     */
    public static function logHttpError($statusCode, $message)
    {
        $rotta = self::getRotta();
        $idCliente = self::getIdCliente();

        // Prova a salvare nel database
        if (self::saveToDatabase($statusCode, $message, $rotta, $idCliente)) {
            return true;
        }

        // Se fallisce, salva in JSON
        return self::logHttpErrorJson($statusCode, $message, $rotta, $idCliente);
    }

    /**
     * Salva nel database
     */
    private static function saveToDatabase($statusCode, $message, $rotta, $idCliente)
    {
        try {
            // Verifica se il database è attivo
            $db = Yii::$app->db;
            $db->open();
            
            if (!$db->getIsActive()) {
                Yii::warning('Database non attivo', 'error_log');
                return false;
            }

            $model = new self();
            $model->response_http = (string) $statusCode;
            $model->message = $message;
            $model->rotta = $rotta;
            $model->id_cliente = $idCliente;

            if ($model->save(false)) {  // false = skip validation
                Yii::info('Errore salvato nel DB: ' . $statusCode, 'error_log');
                return true;
            }

            Yii::warning('Save fallito: ' . print_r($model->errors, true), 'error_log');
            return false;

        } catch (\Exception $e) {
            Yii::error('Eccezione DB: ' . $e->getMessage(), 'error_log');
            return false;
        }
    }

    /**
     * Salva in JSON come fallback
     */
    public static function logHttpErrorJson($statusCode, $message, $rotta = '', $idCliente = null)
    {
        try {
            $data = [
                'response_http' => (string) $statusCode,
                'message' => $message,
                'rotta' => $rotta,
                'id_cliente' => $idCliente,
                'created_at' => date('Y-m-d H:i:s'),
                'source' => 'json_fallback',
            ];

            $jsonDir = Yii::$app->basePath . '/runtime/error_logs';
            $jsonFile = $jsonDir . '/http_errors_' . date('Y-m-d') . '.json';

            if (!is_dir($jsonDir)) {
                mkdir($jsonDir, 0777, true);
            }

            $existingData = [];
            if (file_exists($jsonFile)) {
                $content = file_get_contents($jsonFile);
                $existingData = json_decode($content, true) ?? [];
            }

            $existingData[] = $data;

            $json = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $saved = file_put_contents($jsonFile, $json);

            if ($saved) {
                Yii::info('Errore salvato in JSON: ' . $jsonFile, 'error_log');
            }

            return $saved !== false;

        } catch (\Exception $e) {
            Yii::error('Errore salvataggio JSON: ' . $e->getMessage(), 'error_log');
            return false;
        }
    }

    private static function getRotta()
    {
        try {
            $request = Yii::$app->request;
            if ($request instanceof \yii\web\Request) {
                return (string) $request->getUrl();
            }
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }

    private static function getIdCliente()
    {
        try {
            if (!Yii::$app->user->isGuest) {
                return Yii::$app->user->id;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}