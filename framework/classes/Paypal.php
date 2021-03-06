<?php

class Paypal
{
    //массив для ошибок при работе с апи пейпала
    protected $errors = array();

    //данные(полномочия) песочницы
    protected $credentials = array(
        'USER' => 'ch.kyrill-facilitator_api1.gmail.com',
        'PWD' => 'HTNT6R6EEH7Z76R6',
        'SIGNATURE' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AE.YSMq7OL6JtrdUhwzcO0-hJ0Az',
//        'USER' => 'sdk-three_api1.sdk.com',
//        'PWD' => 'QFZCWN5HZM8VBG7Q',
//        'SIGNATURE' => 'A-IzJhZZjhg29XQ2qnhapuwxIDzyAZQ92FRP5dqBzVesOkzbdUONzmOU',
    );

    //адресс отправки запроса для песочницы(реальный адресс - https://api-3t.paypal.com/nvp (?))

    protected $endPoint = 'https://api-3t.sandbox.paypal.com/nvp';

    /**
     * Версия API
     * @var string
     */
    protected $version = '109.0';

    //метод для запроса($$method  - вид платежа на пейпал, в данном случае Експресс Чекаут)
    public function request($method, $params = array())
    {
        $this->errors = array();
        if (empty($method)) { // Проверяем, указан ли вид платежа
            $this->errors = array('Не указан метод перевода средств');
            return false;
        }

        // Параметры нашего запроса
        $requestParams = array(
                'METHOD' => $method,
                'VERSION' => $this->version
            ) + $this->credentials;

        // Сформировываем данные для NVP
        $request = http_build_query($requestParams + $params);

        // Настраиваем cURL
        $curlOptions = array(
            CURLOPT_URL => $this->endPoint,
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => ROOT_PATH . '/cacert.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        // Отправляем наш запрос, $response будет содержать ответ от API
        $response = curl_exec($ch);

        // Проверяем наличие ошибок в инициализации cURL
        if (curl_errno($ch)) {
            curl_close($ch);
            throw new CurleException(curl_error($ch));
        } else {
            curl_close($ch);
            $responseArray = array();
            parse_str($response, $responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
    }
}
