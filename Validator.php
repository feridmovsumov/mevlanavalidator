<?php
namespace mevlana;

/**
 * Her türlü doğrulama işlemlerini yapmak için kullanılabilecek sınıf
 */
class Validator {

    const ERR_MSG_STRLEN_MAX = "%s length can not be greater than %s";

    const ERR_MSG_STRLEN_MIN = "%s length can not be less than %s";

    const ERR_MSG_STRLEN_BETWEEN = "%s length must be between %s and %s";

    const ERR_MSG_IS_NOT_VALID_EMAIL = "%s is not valid email address";

    const ERR_MSG_NUMBER_BETWEEN = "%s must be between %s and %s";

    const ERR_MSG_NOT_INT = "%s must be an integer";

    const ERR_MSG_NOT_FLOAT = "%s must be float";

    const ERR_MSG_NOT_NUMBER= "%s must be number";
    
    /**
     * Bir dizi içerisindeki bir key'e bağlı verisinin isset ve blank olup olmadığı bilgisini döndürür
     * @param string $key
     * @param array $data
     * @return boolean
     * @author Ferid Mövsümov
     */
    public static function isKeyExistAndNotBlank($key, $data){
        if (! is_array( $data )) {
            throw new \Exception("data parameter must be an array");
        }
        if(!is_string($key)){
            throw new \Exception("key must be a string");
        }
        if(isset($data[$key]) && !Validator::isBlank((string)$data[$key])){
            return true;
        }
        return false;
    }

    /**
     * Stringin valid bir xml olup olmadığını konrol eder
     * @param string $xml
     * @author Ferid Mövsümov
     * @return bool
     */
    public static function isValidXmlString($xml){
        return false !== @simplexml_load_string($xml);
    }

    /**
     * Bir stringin valid bir json olup olmadığına bakar
     * @param string $jsonString
     * @return bool
     * @author Ferid Mövsümov
     */
    public static function isValidJsonString($jsonString){
        if(!is_string($jsonString)){
            return false;
        }
        json_decode($jsonString);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Bir dizinin içerisinde belli keylerin olup olmadığını kontrol eder
     * @param array $array incelenecek dizi
     * @param array $keys kontrol edilecek keyler
     * @return boolean
     * @author Ferid Mövsümov
     */
    public static function doesArrayHasKeys($array, $keys){
        if(!is_array($array)){
            throw new \Exception("first parameter must be an array");
        }

        if(!is_array($keys)){
            throw new \Exception("keys parameter must be an array");
        }

        foreach ($keys as $key){
            if(!array_key_exists($key, $array)){
                return false;
            }
        }

        return true;
    }

    /**
     * Gönderilen slug parametresinin uygun olup olmadığını kontrol eder
     * İzin verilen karakterler ( A–Z, a–z, 0–9, -, _ )
     * @param string $slug
     * @author Ferid
     */
    public static function isValidSlug($slug){
        if (! is_string( $slug )) {
            return false;
        }

        if (! self::strLenBetween( $slug, 1, 100 )) {
            return false;
        }

        return ! preg_match( '/[^a-zA-Z0-9\\_\\-]/', $slug );
    }

    /**
     * Gönderilen domain parametresinin uygun olup olmadığını kontrol eder.
     * http veya https gönderilirmemelidir. Gönderirlirse false döner.
     * www. varsa veya yoksa geri kalan geçerliyse true döner.
     * Subdomain de destekler (örnek : images.google.com)
     * En az 4 karakter en fazla 100 karakter olabilir (a.co 4 karakter olduğu için min bu olmalı)
     * http,https ve ftp falan gönderilirse false dönüyoruz. Kulanıcıların dikkatine!
     * @param strig $domain
     * @return boolean
     */
    public static function isValidDomain($domain){
        if (! is_string( $domain )) {
            return false;
        }

        if (! self::strLenBetween( $domain, 4, 100 )) {
            return false;
        }

        return (bool) preg_match('/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $domain);
    }

    /**
     * Verilen string uzunluğunun $minLength değerinden küçük olması durumunda false döndürür
     * @param string $var
     * @param int $minLength
     * @return bool
     */
    public static function strLenMin($var, $minLength){
        $var = (string) $var;
        $var = trim( $var );

        if (! is_int( $minLength ) || $minLength < 0) {
            throw new \Exception("minLength must be integer greater than or equal to zero");
        }

        if (mb_strlen( $var, 'utf-8' ) < $minLength) {
            return false;
        }

        return true;
    }

    /**
     * String'in uzunluğu gönderilen maxLength değerinden büyükse false döndürür
     * @param string $var
     * @param int $maxLength
     * @return boolean
     */
    public static function strLenMax($var, $maxLength){
        $var = (string) $var;
        $var = trim( $var );

        if (! is_int( $maxLength ) || $maxLength < 0) {
            throw new \Exception("maxLengt must be integer greater than or equal to zero");
        }

        if (mb_strlen( $var, 'utf-8' ) > $maxLength) {
            return false;
        }

        return true;
    }

    /**
     * Bir string'in uzunluğunun belli değerler arasında olması durumunda true
     * aksi durumda false döndürür
     * @param string $var
     * @param int $minLength
     * @param int $maxLength
     * @return boolean
     */
    public static function strLenBetween($var, $minLength, $maxLength){
        $var = (string) $var;
        $var = trim( $var );

        if (self::strLenMin( $var, $minLength ) && self::strLenMax( $var, $maxLength )) {
            return true;
        }

        return false;
    }

    /**
     * Paramtre olarak verilen email adresinin doğru formatta olup olmadığını doğrular
     * @param string $email
     * @return boolean
     */
    public static function isEmail($email){
        if (! is_string( $email )) {
            throw new \Exception("Email must be string");
        }

        if (filter_var( $email, FILTER_VALIDATE_EMAIL )) {
            return true;
        }

        return false;
    }

    /**
     * Parametre olarak gelen değişken float ise true değilse false döndürür
     * @param multitype $variable
     * @return boolean
     */
    public static function isFloat($variable){
        if (is_bool( $variable )) {
            return false;
        }
        if (filter_var( $variable, FILTER_VALIDATE_FLOAT ) !== false) {
            return true;
        }
        return false;
    }

    /**
     * gönderilen değer integer ise true döndürür
     * @param multitype $variable
     * @return boolean
     */
    public static function isInt($variable){
        if (is_bool( $variable )) {
            return false;
        } else if (filter_var( $variable, FILTER_VALIDATE_INT ) !== false) {
            return true;
        }
        return false;
    }

    /**
     * Gönderilen parametre sayısal bir değer ise true döndürür
     * @param multitype $variable
     * @return boolean
     */
    public static function isNumber($variable){
        if (self::isFloat( $variable ) || self::isInt( $variable )) {
            return true;
        }

        return false;
    }

    /**
     * Bir sayının belli değerler arasında olup olmadığını kontrol eder.
     * @param number $number
     * @param number $min
     * @param number $max
     * @return boolean
     */
    public static function isBetween($number, $min, $max){
        if (! self::isNumber( $min ) || ! self::isNumber( $max )) {
            throw new \Exception("min and max parameters must be a number");
        }

        if (! self::isNumber( $number )) {
            throw new \Exception("first parameter must be a number");
        }

        if (($number > $max) || ($number < $min)) {
            return false;
        }

        return true;
    }

    /**
     * 2 şeyin birbirine eşit olup olmadığını kontrole der.
     * @param mixed $expected
     * @param mixed $actual
     */
    public static function equals($expected, $actual){
        if ($expected == $actual) {
            return true;
        }
        return false;
    }

    /**
     * Tarihin Y-m-d H:i:s formatında bir string olup olmadığını kontrol eder.
     * @return boolean
     */
    public static function isValidMysqlTimestamp($dateStr){
        $arr = explode( " ", $dateStr );
        if (! is_array( $arr ) || count( $arr ) != 2) {
            return false;
        }
        $dateArr = explode( "-", $arr [0] );
        if (! is_array( $dateArr ) || count( $dateArr ) != 3) {
            return false;
        }
        $timeArr = explode( ":", $arr [1] );
        if (! is_array( $timeArr ) || count( $timeArr ) != 3) {
            return false;
        }
        if (date( 'Y-m-d H:i:s', mktime( $timeArr [0], $timeArr [1], $timeArr [2], $dateArr [1], $dateArr [2], $dateArr [0] ) ) != $dateStr) {
            return false;
        }
        return true;
    }

    /**
     * Tarihin Y-m-d formatında bir string olup olmadığını kontrol eder.
     * @param string $dateStr
     * @return boolean
     * @author sedat
     */
    public static function isValidMysqlDate($dateStr) {
        $dateArr = explode('-', $dateStr);
        if(!is_array($dateArr) || count($dateArr) !== 3) {
            return false;
        }
        if(date('Y-m-d', mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0])) != $dateStr) {
            return false;
        }
        return true;
    }

    /**
     * Bunların hepsi blank sayılacak:  "", " ", "   "
     * @param string $string
     * @return bool
     * @author Ferid Mövsümov
     */
    public static function isBlank($string){
        if (! is_string( $string )) {
            throw new \Exception("Type of parameter passed to isBlank metod must be a string");
        }

        return ! self::strLenMin( $string, 1 );
    }

    /**
     * String'in virgüllerle ayrılmış id değerlerinden oluştuğunu doğrular
     * @param string $ids
     * @return bool
     * @author Ferid Mövsümov
     */
    public static function isValidIdsSeperatedWithCommas($ids){
        if (! is_string( $ids )) {
            throw new \Exception("ids parameter must be a string");
        }

        if (self::isBlank( $ids )) {
            return true;
        }

        $explodedIds = explode( ",", $ids );

        foreach ($explodedIds as $id) {

            if (! self::isInt( $id )) {
                return false;
            }

            $id = (int) $id;

            if ($id <= 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kendisine parametre olarak gelen timezone değişkenin
     * uygun bir değişkeni olup olmadığına bakar.
     * Eğer uygun değilse false verir.
     * Eğer uygun ise true döner.
     *
     * @param string $timezone
     * @return bool
     * @author Turker Senturk
     */
    public static function isValidTimezone($timezone){
        $timezone = trim($timezone);
        try{
            new \DateTimeZone($timezone);
            return true;
        }catch (\Exception $ex){
            return false;
        }
    }

    /**
     * Gönderilen string'in valid bir ip adresi olup olmadığı bilgisini
     * döndürür.
     *
     * @author adem
     * @param string $ipAddress
     * @return boolean
     */
    public static function isValidIpAddress($ipAddress){
        $ipAddress = (string) $ipAddress;
        return  ( filter_var($ipAddress, FILTER_VALIDATE_IP) ? TRUE : FALSE ) ;
    }

    /**
     * Gönderilen kredi kartı numarasının geçerli olup olmadığını şu bilgilere bakarak kontrol eder:
     *  - sadece rakamlardan mı oluşuyor?
     *  - uzunluğu 14 ile 20 arasında mı?(14 ile 20 dahil)
     * @param string $ccNumber
     * @return boolean
     * @author sedat
     */
    public static function isValidCCNumber($ccNumber) {
        $ccNumber = trim($ccNumber);
        if(preg_match('/[^\d+]/', $ccNumber) > 0) {
            return false;
        }
        if(false === self::strLenBetween($ccNumber, 14, 20)) {
            return false;
        }
        return true;
    }

    /**
     * Gönderilen kredi kartı son geçerlilik ayının geçerli olup olmadığını şu bilgilere bakarak kontrol eder:
     *  - uzunluğu 2 karakter mi?
     *  - sadece rakamlardan mı oluşuyor?
     *  - pozitif tamsayı ve en fazla 12 mi?
     * @param string $month
     * @return boolean
     * @author sedat
     */
    public static function isValidCCExpMonth($month) {
        $month = trim($month);
        if(false === self::strLenBetween($month, 2, 2)) {
            return false;
        }
        if(preg_match('/[^\d+]/', $month) > 0) {
            return false;
        }
        $month = intval($month);
        if($month > 12 || $month <= 0) {
            return false;
        }
        return true;
    }

    /**
     * Gönderilen kredi kartı son geçerlilik yılının geçerli olup olmadığını şu bilgilere bakarak kontrol eder:
     *  - uzunluğu 2 karakter mi?
     *  - sadece rakamlardan mı oluşuyor?
     *  - pozitif tamsayı mı?
     * @param string $year
     * @return boolean
     * @author sedat
     */
    public static function isValidCCExpYear($year,$yearLength=2) {
        $year = trim($year);
        if(false === self::strLenBetween($year, $yearLength, $yearLength)) {
            return false;
        }
        if(preg_match('/[^\d+]/', $year) > 0) {
            return false;
        }
        $year = intval($year);
        if($year == 0) {
            return false;
        }
        return true;
    }

    /**
     * Gönderilen kredi kartı sahibi isminin geçerli olup olmadığını şu bilgilere bakarak kontrol eder:
     *  - uzunluğu 1 ile 512 karakter arasında mı?
     * @param string $name
     * @return boolean
     * @author sedat
     */
    public static function isValidCCName($name) {
        $name = trim($name);
        if(false === self::strLenBetween($name, 1, 512)) {
            return false;
        }
        return true;
    }

    /**
     * Gönderilen kredi kartı cvc bilgisinin geçerli olup olmadığını
     * @param string $cvc
     * @return boolean
     */
    public static function isValidCCCvc($cvc) {
        $cvc = trim($cvc);
        if(false === self::strLenBetween($cvc, 3, 4)) {
            return false;
        }
        if(preg_match('/[^\d+]/', $cvc) > 0) {
            return false;
        }
        return true;
    }
}
