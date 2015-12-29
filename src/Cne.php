<?php

namespace CNE;

/**
 * Para utilizar, si el servidor no tiene habilitado "allow_url_fopen"
 */
define ("CURL_METHOD", 0);
/**
 * Para utilizar, si el servidor tiene habilitado "allow_url_fopen"
 */
define ("FILE_METHOD", 1);

/**
 * La clase CNE contiene todos los metodos y atributos para realizar el chequeo de cualquier cedula mediante el la pagina web del CNE
 */
class Cne
{
    private $nationality;
    private $dni;

    /**
     * Constructor de la clase, los parametros son opcionales, se puede utilizar el setter para establecer los valores
     * @param $nationality
     * @param $dni
     */
    function __construct($nationality = null, $dni = null)
    {
        $this->nationality = $nationality;
        $this->dni = $dni;
    }

    /**
     * Imprime los atributos de la clase
     */
    public function getData()
    {
        return array($this->nationality, $this->dni);
    }

    /**
     * Establece los valores para los atributos de la clase
     * @param $nationality
     * @param $dni
     */
    public function setData($nationality, $dni)
    {
        $this->nationality = $nationality;
        $this->dni = $dni;
    }

    /**
     * Permite buscar los datos mediante la url del CNE
     * Puede ser accedida por metodos difentes, CURL & FOPEN
     * Ver 'allow_url_fopen'
     * @param $method
     * @return string
     */
    public function search($method = FILE_METHOD)
    {
        $url = "http://www.cne.gov.ve/web/registro_electoral/ce.php?nacionalidad=$this->nationality&cedula=$this->dni";

        if ($method) {
            $result = strip_tags($this->getFileData($url));
        } else {
            $result = strip_tags($this->getCurlData($url));
        }
        $final = $this->refactorData($result);
        return json_encode($final);
    }

    /**
     * Metodo CURL para la obtener los datos
     * @param $url
     * @return mixed
     */
    private function getCurlData($url)
    {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($channel, CURLOPT_HEADER, FALSE);
        if (curl_exec($channel) === false) {
            echo 'Curl error: ' . curl_error($channel);
        } else {
            $content = curl_exec($channel);
        }
        curl_close($channel);
        return $content;
    }


    /**
     * Metodo FILE para la obtener los datos
     * @param $url
     * @return string
     */
    private function getFileData($url)
    {
        return file_get_contents($url);
    }

    /**
     * Permite separar la data en los campor especificados en el array "Look" y eliminar los espacios en blanco, retornos de carro y tabulaciones.
     * @param $result
     * @return array
     */
    private function refactorData($result)
    {
        if (strstr($result, 'DATOS DEL ELECTOR')) {
            $look = array('Cédula:', 'Nombre:', 'Estado:', 'Municipio:', 'Parroquia:', 'Centro:', 'Dirección:', 'SERVICIO ELECTORAL', 'Mesa:','Imprimir', 'Cerrar');
            $info = explode("@", trim(str_replace($look, '@', $result)));
            $name = explode(" ", $info[2]);
            return array("Status" => "OK",
                "CI" => preg_replace('/(\v|\s)+/', ' ', trim($info[1])),
                "Primer Nombre" => preg_replace('/(\v|\s)+/', ' ', trim($name[0])),
                "Segundo Nombre" => preg_replace('/(\v|\s)+/', ' ', trim($name[1])),
                "Primer Apellido" => preg_replace('/(\v|\s)+/', ' ', trim($name[count($name) - 2])),
                "Segundo Apellido" => preg_replace('/(\v|\s)+/', ' ', trim($name[count($name) - 1])),
                "Estado" => preg_replace('/(\v|\s)+/', ' ', trim($info[3])),
                "Municipio" => preg_replace('/(\v|\s)+/', ' ', trim($info[4])),
                "Parroquia" => preg_replace('/(\v|\s)+/', ' ', trim($info[5])),
                "Centro" => preg_replace('/(\v|\s)+/', ' ', trim($info[6])),
                "Servicio" => preg_replace('/(\v|\s)+/', ' ', trim($info[8])),
            );
        } else {
            return array("Status" => "ERR",
                "Description" => "La cédula no es valida, o el elector esta inhabilitado");
        }
    }
}