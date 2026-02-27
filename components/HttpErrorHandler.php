<?php


namespace app\components;


use Yii;

use yii\web\ErrorHandler as BaseErrorHandler;

use yii\web\HttpException;


class HttpErrorHandler extends BaseErrorHandler

{

    private $errorCodes = [400, 401, 403, 404, 405, 422, 429, 500, 502, 503];


    public function handleException($exception)

    {

        $this->saveHttpError($exception);

        parent::handleException($exception);

    }


    private function saveHttpError($exception)

    {

        $statusCode = 500;

        $message = $exception->getMessage();


        if ($exception instanceof HttpException) {

            $statusCode = $exception->statusCode;

        } elseif ($exception instanceof \yii\base\UserException) {

            $statusCode = 400;

        }


        if (!in_array($statusCode, $this->errorCodes)) {

            return;

        }


        try {

            \app\models\ErrorLogHttp::logHttpError($statusCode, $message);

        } catch (\Exception $e) {

            \app\models\ErrorLogHttp::logHttpErrorJson($statusCode, $message);

        }

    }

}