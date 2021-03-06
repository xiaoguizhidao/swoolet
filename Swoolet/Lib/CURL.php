<?php

namespace Swoolet\Lib;

use Swoolet\App;

class CURL
{
    public $error = '', $params = array();

    public $options = array(
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
    );

    public function __construct(array $options = array())
    {
        $this->options = $options + App::getConfig('curl') + $this->options;
    }

    public function post($url, $data = array(), array $options = array())
    {
        $options += array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        );

        return $this->exec($url, $options);
    }

    /**
     * get data like curl -d:
     * get('http://example.com', array(), array(\CURLOPT_POSTFIELDS => $string_data))
     *
     * @param $url
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function get($url, $data = array(), array $options = array())
    {

        if ($data) {
            if (is_array($data))
                $data = http_build_query($data);

            $url .= '?' . $data;
        }
        /*
        $options += array(
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $data,
        );
        */

        return $this->exec($url, $options);
    }

    public function put($url, $data = array(), array $options = array())
    {
        $options += array(
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data,
        );

        return $this->exec($url, $options);
    }

    public function delete($url, array $options = array())
    {
        $options += array(
            CURLOPT_CUSTOMREQUEST => 'DELETE',
        );

        return $this->exec($url, $options);
    }

    protected function exec($url, array $options = array())
    {
        $ch = curl_init();

        $options[CURLOPT_URL] = $url;

        curl_setopt_array($ch, $this->params = $options + $this->options);

        $result = curl_exec($ch);

        $this->error = curl_error($ch);

        curl_close($ch);

        return $result;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getParams()
    {
        return $this->params;
    }
}