<?php
/* V: 1.0.0 */
class mzParams
{
    //===============================================================================// Params
    private static $params = [];
    private static $errors = [];
    private static $_HEADERS;
    //===============================================================================// data
    //============================================================// headers
    static function headers(string $name = null)
    {
        if (empty(mzParams::$_HEADERS)) mzParams::$_HEADERS = getallheaders();
        if (!empty($name)) return @mzParams::$_HEADERS[$name];
        return @mzParams::$_HEADERS;
    }
    //============================================================// inputs
    static function inputs(string $name = null)
    {
        if (!empty($name)) return @$_REQUEST[$name];
        return @$_REQUEST;
    }
    //============================================================// files
    static function files(string $name = null)
    {
        if (!empty($name)) {
            if (@$_FILES[$name] === array()) {
                $res = [];
                foreach ($_FILES[$name] as $i => $v) {
                    $res[] = [
                        "name" => $_FILES[$name]['name'][$i],
                        "type" => $_FILES[$name]['type'][$i],
                        "tmp_name" => $_FILES[$name]['tmp_name'][$i],
                        "error" => $_FILES[$name]['error'][$i],
                        "size" => $_FILES[$name]['size'][$i],
                    ];
                }
                return $res;
            }
            return @$_FILES[$name];
        }
        return @$_FILES;
    }
    //===============================================================================// Keys
    //============================================================// encrypt
    public static function encrypt(string $name, array $value = null, string $key = "")
    {
        // valid
        mzParams::$params[$name] = null;
        if (empty($value)) return mzParams::$errors[$name] = "required";
        // encrypt
        try {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            // cipher
            $cipher = "aes-128-ctr";
            if (!in_array($cipher, openssl_get_cipher_methods())) return mzParams::$errors[$name] = "cipher_invalid";
            $options = 0;
            $iv_length = openssl_cipher_iv_length($cipher);
            $pseudo_bytes = openssl_random_pseudo_bytes($iv_length);
            $encryption = @openssl_encrypt($value, $cipher, $key, $options, $pseudo_bytes);
            $value = "$encryption:" . base64_encode($pseudo_bytes);
            // encode
            $value = base64_encode($value);
        } catch (Exception $e) {
            return mzParams::$errors[$name] = "cannot_encrypt=$e";
        }
        // return
        mzParams::$params[$name] = $value;
    }
    //============================================================// decrypt
    public static function decrypt(string $name, string $value = null, string $key = "")
    {
        // valid
        mzParams::$params[$name] = null;
        if (empty($value)) return mzParams::$errors[$name] = "required";
        // decrypt
        try {
            $value = base64_decode($value);
            $value = explode(":", $value);
            // cipher
            $cipher = "aes-128-ctr";
            if (!in_array($cipher, openssl_get_cipher_methods())) return mzParams::$errors[$name] = "cipher_invalid";
            $options = 0;
            $value = @openssl_decrypt(@$value[0], $cipher, $key, $options, base64_decode(@$value[1]));
            if ($value == null) return mzParams::$errors[$name] = "invalid_decrypt";
            // decode
            $value = json_decode($value, true);
        } catch (Exception $e) {
            return mzParams::$errors[$name] = "cannot_decrypt=$e";
        }
        // return
        mzParams::$params[$name] = $value;
    }
    //===============================================================================// Add
    //============================================================// Param
    public static function addParam(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, array $_check_for_types = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    mzParams::$params[$name][$i] = [];
                    $errors = [];
                    foreach ($_check_for_types as $type) {
                        switch ($type) {
                            case "email":
                                $rs = mzParams::_checkEmail($v, $is_required, false, $min_length, $max_length);
                                if ($rs->status == 200) mzParams::$params[$name][$i][$type] = $rs->data;
                                else $errors[$type] = $rs->error;
                                break;
                            case "telephone":
                                $rs = mzParams::_checkTelephone($v, $is_required, $min_length, $max_length);
                                if ($rs->status == 200) mzParams::$params[$name][$i][$type] = $rs->data;
                                else $errors[$type] = $rs->error;
                                break;
                        }
                    }
                    if (Count($errors) == Count($_check_for_types)) mzParams::$errors[$name][$i] = $errors;
                }
            }
        } else {
            $v = mzParams::$params[$name];
            mzParams::$params[$name] = [];
            $errors = [];
            foreach ($_check_for_types as $type) {
                switch ($type) {
                    case "email":
                        $rs = mzParams::_checkEmail($v, $is_required, false, $min_length, $max_length);
                        if ($rs->status == 200)  mzParams::$params[$name][$type] = $rs->data;
                        else $errors[$type] = $rs->error;
                        break;
                    case "telephone":
                        $rs = mzParams::_checkTelephone($v, $is_required, $min_length, $max_length);
                        if ($rs->status == 200)  mzParams::$params[$name][$type] = $rs->data;
                        else $errors[$type] = $rs->error;
                        break;
                }
            }
            if (Count($errors) == Count($_check_for_types)) mzParams::$errors[$name] = $errors;
        }
    }
    //============================================================// Int
    public static function addInt(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkInt($v, $is_required, $min_length, $max_length);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkInt(mzParams::$params[$name], $is_required, $min_length, $max_length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Float
    public static function addFloat(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, array $format_options = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkFloat($v, $is_required, $min_length, $max_length, $format_options);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkFloat(mzParams::$params[$name], $is_required, $min_length, $max_length, $format_options);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// String
    public static function addString(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, array $format_options = null, array $_check_for_values = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkString($v, $is_required, $min_length, $max_length, $format_options, $_check_for_values);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkString(mzParams::$params[$name], $is_required, $min_length, $max_length, $format_options, $_check_for_values);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Email
    public static function addEmail(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, bool $_check_domain = false, int $min_length = null, int $max_length = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkEmail($v, $is_required, $_check_domain, $min_length, $max_length);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkEmail(mzParams::$params[$name], $is_required, $_check_domain, $min_length, $max_length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Domain
    public static function addDomain(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkDomain($v, $is_required, $min_length, $max_length);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkDomain(mzParams::$params[$name], $is_required, $min_length, $max_length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Coordinates
    public static function addCoordinates(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkCoordinates($v, $is_required, $min_length, $max_length);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkCoordinates(mzParams::$params[$name], $is_required, $min_length, $max_length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Telephone
    public static function addTelephone(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkTelephone($v, $is_required, $min_length, $max_length);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkTelephone(mzParams::$params[$name], $is_required, $min_length, $max_length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Link
    public static function addLink(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkLink($v, $is_required, $min_length, $max_length);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkLink(mzParams::$params[$name], $is_required, $min_length, $max_length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Telephone
    public static function addTimestamp(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, array $format_options = null,  int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkTimestamp($v, $is_required, $min_length, $max_length, $format_options);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkTimestamp(mzParams::$params[$name], $is_required, $min_length, $max_length, $format_options);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// Telephone
    public static function addColor(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $min_length = null, int $max_length = null, array $format_options = null,  int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::inputs()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::inputs($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkColor($v, $is_required, $min_length, $max_length, $format_options);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkColor(mzParams::$params[$name], $is_required, $min_length, $max_length, $format_options);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// UploadFile
    public static function addUploadFile(string $name, $value = null, bool $is_required = false, bool $is_uploaded = false, bool $exclude = false, bool $is_array = false, int $_check_for_ext = null, int $max_size_in_kilobytes = null,  int $array_min_items = null, int $array_max_items = null)
    {
        // upload value
        if ($is_uploaded == true && !array_key_exists($name, mzParams::files()) && $exclude == true) return;
        if ($is_uploaded == true) mzParams::$params[$name] = mzParams::files($name);
        else mzParams::$params[$name] = $value;
        // array
        if ($is_array == true) {
            // validate array
            $rs = mzParams::_checkArray(mzParams::$params[$name], $is_required, $array_min_items, $array_max_items);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
            // validate values
            if (!empty(mzParams::$params[$name])) {
                foreach (mzParams::$params[$name] as $i => $v) {
                    $rs = mzParams::_checkUploadFile($v, $is_required, $_check_for_ext, $max_size_in_kilobytes);
                    if ($rs->status != 200) {
                        mzParams::$errors[$name] = [];
                        return mzParams::$errors[$name][$i] = $rs->error;
                    }
                    mzParams::$params[$name][$i] = $rs->data;
                }
            }
        } else {
            $rs = mzParams::_checkUploadFile(mzParams::$params[$name], $is_required, $_check_for_ext, $max_size_in_kilobytes);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //===============================================================================// get
    //============================================================// Token
    public static function getToken(string $name, int $length, int $array_length = null)
    {
        if (!empty($array_length)) {
            mzParams::$params[$name] = [];
            for ($i = 0; $i < $array_length; $i++) {
                $rs = mzParams::createToken($length);
                if ($rs->status != 200) {
                    mzParams::$errors[$name] = [];
                    return mzParams::$errors[$name][$i] = $rs->error;
                }
                mzParams::$params[$name][] = $rs->data;
            }
        } else {
            $rs = mzParams::createToken($length);
            if ($rs->status != 200) return mzParams::$errors[$name] = $rs->error;
            mzParams::$params[$name] = $rs->data;
        }
    }
    //============================================================// ClientAgent
    public static function getClientAgent(string $name)
    {
        $v = @getenv('HTTP_USER_AGENT');
        $res = [];
        $b = 0;
        $l = 0;
        for ($i = 0; $i < strlen($v); $i++) {
            if ($v[$i] == "(") {
                if ($b == 0) {
                    $res[] = substr($v, $l, $i - $l);
                    $l = $i + 1;
                }
                $b++;
            }
            if ($v[$i] == ")") {
                $b--;
                if ($b == 0) {
                    $res[] = substr($v, $l, $i - $l);
                    $l = $i + 1;
                }
            }
            if ($i == (strlen($v) - 1)) $res[] = substr($v, $l, $i - $l);
        }
        // device has ; as seperators
        $agent = $v;
        foreach ($res as $r) {
            if (strpos($r, ";") != false)  $agent = $r;
        }
        mzParams::$params[$name] = $agent;
    }
    //============================================================// ClientIp
    public static function getClientIp(string $name)
    {
        // ip & IPv6        
        $ip = htmlspecialchars(@getenv('REMOTE_ADDR'));
        if (strpos($ip, '::') === 0) $ip = substr($ip, strrpos($ip, ':') + 1);
        // return
        mzParams::$params[$name] = long2ip(ip2long($ip));
    }
    //===============================================================================// Upload
    //============================================================// UploadFile
    public static function uploadFile(string $name, string $path, string $filename = null)
    {
        //
        $files = @mzParams::$params[$name];
        //
        if (empty($files)) return;
        if ($files === array()) {
            foreach (mzParams::$params[$name] as $i => $v) {
                // upload value
                if (empty(@mzParams::$params[$name][$i]['tmp_name'])) return mzParams::$errors[$name][$i] = "file_invalid";
                if (!is_dir($path)) return mzParams::$errors[$name][$i] = "upload_path_doesnot_exist";
                //
                if (empty($filename)) $filename = mzParams::$params[$name][$i]["name"];
                else $filename .= "-$i." . pathinfo(mzParams::$params[$name][$i]["name"], PATHINFO_EXTENSION);
                //
                if (!move_uploaded_file(mzParams::$params[$name][$i]['tmp_name'], $path . DIRECTORY_SEPARATOR . $filename)) return mzParams::$errors[$name][$i] = "unable_to_upload_file";
                mzParams::$params[$name][$i] = $filename;
            }
            return mzAPI::return(200);
        } else {
            // upload value
            if (empty(@mzParams::$params[$name]['tmp_name'])) return mzParams::$errors[$name] = "file_invalid";
            if (!is_dir($path)) return mzParams::$errors[$name] = "upload_path_doesnot_exist";
            //
            if (empty($filename)) $filename = mzParams::$params[$name]["name"];
            else $filename .= "." . pathinfo(mzParams::$params[$name]["name"], PATHINFO_EXTENSION);
            //
            if (!move_uploaded_file(mzParams::$params[$name]['tmp_name'], $path . DIRECTORY_SEPARATOR . $filename)) return mzParams::$errors[$name] = "unable_to_upload_file";
            mzParams::$params[$name] = $filename;
            return mzAPI::return(200);
        }
    }
    //===============================================================================// Validators
    //============================================================// Array
    private static function _checkArray($value = null, bool $is_required = false, int $array_min_items = null, int $array_max_items = null): object
    {
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        if ((!empty($value) || $value == 0) && !is_array($value)) return mzAPI::return(400, "invalid_array");
        if (!empty($array_min_items) && Count($value) < $array_min_items) return mzAPI::return(400, "min_items=" . $array_min_items);
        if (!empty($array_max_items) && Count($value) > $array_max_items) return mzAPI::return(400, "max_items=" . $array_max_items);
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Int
    private static function _checkInt($value = null, bool $is_required = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if (is_array($value)) return mzAPI::return(400, "invalid_int");
        if ($is_required == true && (empty($value) && strlen($value) == 0)) return mzAPI::return(400, "required");
        if ($is_required == false && (empty($value) || strlen($value) == 0)) return mzAPI::return(200);
        // valid
        if (!is_numeric($value)) return mzAPI::return(400, "invalid_int");
        $value = intval($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Float
    private static function _checkFloat($value = null, bool $is_required = false, int $min_length = null, int $max_length = null, array $format_options = null): object
    {
        // _check if required and empty
        if (is_array($value)) return mzAPI::return(400, "invalid_float");
        if ($is_required == true && (empty($value) && strlen($value) == 0)) return mzAPI::return(400, "required");
        if ($is_required == false && (empty($value) || strlen($value) == 0)) return mzAPI::return(200);
        // valid
        if (!is_numeric($value)) return mzAPI::return(400, "invalid_float");
        $value = floatval($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // format
        if (!empty($format_options)) {
            $value = mzParams::format($value, $format_options);
        }
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// String
    private static function _checkString($value = null, bool $is_required = false, int $min_length = null, int $max_length = null, array $format_options = null, array $_check_for_values = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        if (!is_string($value)) return mzAPI::return(400, "invalid_string");
        $value = strVal($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // format
        if (!empty($format_options)) {
            $value = mzParams::format($value, $format_options);
        }
        // values
        if (!empty($_check_for_values) && !in_array($value, $_check_for_values)) return mzAPI::return(400, "incorrect_value=" . implode(',', $_check_for_values));
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Email
    private static function _checkEmail($value = null, bool $is_required = false, bool $_check_domain = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) return mzAPI::return(400, "invalid_email");
        if ($_check_domain == true && !checkdnsrr(substr($value, strpos($value, "@") + 1))) return mzAPI::return(400, "invalid_email_domain");
        $value = strVal($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        $value = mzParams::format($value, ['trim', 'lowercase']);
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Domain
    private static function _checkDomain($value = null, bool $is_required = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        if (!checkdnsrr($value)) return mzAPI::return(400, "invalid_domain");
        $value = strVal($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        $value = mzParams::format($value, ['trim']);
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Coordinates
    private static function _checkCoordinates($value = null, bool $is_required = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        $value = mzParams::format($value, ['trimSpaces']);
        if (preg_match("/^(?<latitude>[-]?[0-8]?[0-9]\.\d+|[-]?90\.0+?)(?<delimeter>.)(?<longitude>[-]?1[0-7][0-9]\.\d+|[-]?[0-9]?[0-9]\.\d+|[-]?180\.0+?)$/", $value) == 0) return mzAPI::return(400, "invalid_coordinates");
        $value = strVal($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Telephone
    private static function _checkTelephone($value = null, bool $is_required = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        $value = mzParams::format($value, ['trimSpaces']);
        $value = str_replace(['.', '-', '(', ')', '+'], '', $value);
        if (preg_match("/^[+][0-9]/", $value) == 0 && preg_match("/^[0-9]/", $value) == 0) return mzAPI::return(400, "invalid_telephone");
        $value = strVal($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Link
    private static function _checkLink($value = null, bool $is_required = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        if (!filter_var($value, FILTER_VALIDATE_URL))  return mzAPI::return(400, "invalid_url");
        $value = strVal($value);
        // length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Timestamp
    private static function _checkTimestamp($value = null, bool $is_required = false, string $min_date = null, string $max_date = null, array $format_options = null): object
    {
        // _check if required and empty
        if ($value == "NOW") $value = date("Y-m-d H:i:s");
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // valid
        if (strtotime($value) == false)  return mzAPI::return(400, "invalid_timestamp");
        $value = strVal($value);
        // length
        if (!empty($min_date) && strtotime($min_date) != false && strtotime($value) < strtotime($min_date)) return mzAPI::return(400, "min_date=" . $min_date);
        if (!empty($max_date)  && strtotime($max_date) != false && strtotime($value) > strtotime($min_date)) return mzAPI::return(400, "max_date=" . $max_date);
        // format
        if (!empty($format_options)) $value = mzParams::format($value, $format_options);
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// Color
    private static function _checkColor($value = null, bool $is_required = false, int $min_length = null, int $max_length = null): object
    {
        // _check if required and empty
        if ($is_required == true && empty($value)) return mzAPI::return(400, "required");
        // if empty and not required return null
        if ($is_required == false && empty($value)) return mzAPI::return(200);
        // some needed formating
        $value = mzParams::format($value, ['trimSpaces', 'lowercase']);
        // _check if matches regex
        if (
            preg_match("#(([a-f]){2}([a-f]|[0-9]){6}|([a-f]|[0-9]){6}|([a-f]){1}([a-f]|[0-9]){3}|([a-f]|[0-9]){3})", $value) == 0 // any hex
            && preg_match("rgb(\((([0-9]){1}|([0-9]){2}|([0-9]){3}),(([0-9]){1}|([0-9]){2}|([0-9]){3}),(([0-9]){1}|([0-9]){2}|([0-9]){3})\)|a\((([0-9]){1}|([0-9]){2}|([0-9]){3}),(([0-9]){1}|([0-9]){2}|([0-9]){3}),(([0-9]){1}|([0-9]){2}|([0-9]){3}),(([0-9]){1}|([0-9]){2}|([0-9]){3})\))", $value) == 0 // rgb & rgba
            && preg_match("hsl(\(\s*(\d+)\s*,\s*(\d*(?:\.\d+)?%)\s*,\s*(\d*(?:\.\d+)?%)\)|(a\((\d+),\s*([\d.]+)%,\s*([\d.]+)%,\s*(\d*(?:\.\d+)?)\)))", $value) == 0 // hsl & hsla
        ) return mzAPI::return(400, "invalid_color");
        $value = strVal($value);
        // _check length
        if (!empty($min_length) && strlen($value) < $min_length) return mzAPI::return(400, "min_length=" . $min_length);
        if (!empty($max_length) && strlen($value) > $max_length) return mzAPI::return(400, "max_length=" . $max_length);
        // return
        return mzAPI::return(200, null, null, $value);
    }
    //============================================================// UploadFile
    private static function _checkUploadFile($file = null, bool $is_required = false, array $_check_for_ext = null, int $max_size_in_kilobytes = null): object
    {
        // _check if required and empty
        if ($is_required == true && (!file_exists(@$file['tmp_name']) || !is_uploaded_file(@$file['tmp_name']))) return mzAPI::return(400, "required");
        if ($is_required == false && (!file_exists(@$file['tmp_name']) || !is_uploaded_file(@$file['tmp_name']))) return mzAPI::return(200);
        // valid
        if (UPLOAD_ERR_OK !== @$file["error"]) return mzAPI::return(400, "file_error");
        if (!empty($_check_for_ext) && !in_array(strtolower(pathinfo(@$file["name"], PATHINFO_EXTENSION)), array_map('strtolower', $_check_for_ext))) return mzAPI::return(400, "file_error");
        if (!empty($max_size_in_kilobytes) && @$file["size"] > ($max_size_in_kilobytes * 1000)) return mzAPI::return(400, "max_file_size=$max_size_in_kilobytes");
        // return
        return mzAPI::return(200, null, null, $file);
    }
    //============================================================// Token
    private static function createToken(int $length): object
    {
        // create
        $token = bin2hex(openssl_random_pseudo_bytes(ceil($length / 2), $cstrong));
        $token = substr_replace($token, "", $length, strlen($token) - 1);
        // return
        return mzAPI::return(200, null, null, $token);
    }
    //===============================================================================// functions
    //============================================================// errors
    public static function errors(string $n = null)
    {
        if (!empty($n)) return @mzParams::$errors[$n];
        else if (!empty(mzParams::$errors)) return mzParams::$errors;
        return false;
    }
    //============================================================// params
    public static function params(string $parameter = null, array $format_options = null, array &$array_push = null)
    {
        if (!empty($parameter)) {
            // check for a rename
            $param = explode("=", $parameter);
            $name = @$param[1];
            $keys = explode("/", $param[0]);
            $val = @mzParams::$params[@$keys[0]];
            // apply keys by searching in param
            for ($i = 1; $i < COUNT($keys); $i++) {
                $val = @$val[$keys[$i]];
            }

            //format if available
            if (!empty($format_options)) {
                $val = mzParams::format($val, $format_options);
            }

            //return
            if (isset($array_push)) {
                if (!empty($name)) $array_push[$name] = $val;
                else $array_push[$parameter] = $val;
                return $array_push;
            }
            return $val;
        } 
        //return all if nothings needed
        return mzParams::$params;
    }
    //============================================================// format
    public static function format($value, array $format_options)
    {
        $formats = [
            "round" => function ($value) {
                return round($value);
            },
            "ceil" => function ($value) {
                return ceil($value);
            },
            "floor" => function ($value) {
                return floor($value);
            },
            "currency" => function ($value) {
                return round($value, 2);
            },
            "decimal" => function ($value, $decimals) {
                return round($value, $decimals);
            },
            "readable" => function ($value) {
                return number_format($value, 2, ".", ",");
            },
            //================
            "hash" => function ($value) {
                return password_hash($value, PASSWORD_BCRYPT, ["cost" => 8]);
            },
            "lowercase" => function ($value) {
                return strtolower($value);
            },
            "uppercase" => function ($value) {
                return strtoupper($value);
            },
            "capitalize" => function ($value) {
                return ucwords(strtolower($value));
            },
            "trim" => function ($value) {
                $val = $value;
                while (strpos($val, "  ") != false) $val = str_replace("  ", " ", $val);
                return trim($val);
            },
            "trimSpaces" => function ($value) {
                $val = $value;
                while (strpos($val, " ") != false) $val = str_replace(" ", "", $val);
                return trim($val);
            },
            "striptags" => function ($value) {
                return strip_tags($value);
            },
            "htmlspecialchars" => function ($value) {
                return htmlspecialchars($value);
            },
            "htmlspecialchars_decode" => function ($value) {
                return htmlspecialchars_decode($value);
            },
            "utf8encode" => function ($value) {
                return utf8_encode($value);
            },
            "utf8decode" => function ($value) {
                return utf8_decode($value);
            },
            "urlencode" => function ($value) {
                return urlencode($value);
            },
            "urldecode" => function ($value) {
                return urldecode($value);
            },
            "ltrim" => function ($value, String $trim) {
                return ltrim($value, $trim);
            },
            "rtrim" => function ($value, String $trim) {
                return rtrim($value, $trim);
            },
            "explode" => function ($value, String $trim) {
                $arr = explode($trim, $value);
                return array_filter($arr);
            },
            //================
            'date' => function ($value) {
                return date("Y-m-d", strtotime($value));
            },
            'time' => function ($value) {
                return date("H:i:s", strtotime($value));
            },
            'datetime' => function ($value) {
                return date("Y-m-d H:i:s", strtotime($value));
            },
            'unix' => function ($value) {
                return date("U", strtotime($value));
            },
            'second' => function ($value) {
                return date("s", strtotime($value));
            },
            'minute' => function ($value) {
                return date("i", strtotime($value));
            },
            'hour' => function ($value) {
                return date("G", strtotime($value));
            },
            'weekday' => function ($value) {
                $w = date("N", strtotime($value)) + 1;
                if ($w == 8) $w = 1;
                return $w;
            },
            'monthday' => function ($value) {
                return date("j", strtotime($value));
            },
            'yearday' => function ($value) {
                return date("z", strtotime($value)) + 1;
            },
            'weekdayFull' => function ($value) {
                return strtolower(date("l", strtotime($value)));
            },
            'weekdayShort' => function ($value) {
                return strtolower(date("D", strtotime($value)));
            },
            'week' => function ($value) {
                return date("W", strtotime($value));
            },
            'month' => function ($value) {
                return date("n", strtotime($value));
            },
            'monthtext' => function ($value) {
                return strtolower(date("F", strtotime($value)));
            },
            'monthdays' => function ($value) {
                return date("t", strtotime($value));
            },
            'year' => function ($value) {
                return date("Y", strtotime($value));
            },
            'meridiem' => function ($value) {
                return date("a", strtotime($value));
            },
            'timezone' => function ($value) {
                return date("P", strtotime($value));
            },
            'timezoneoffset' => function ($value) {
                return date("Z", strtotime($value));
            },
        ];

        foreach ($format_options as $k => $v) {
            if (is_int($k)) $k = $v;
            if (isset($formats[$k])) $value = $formats[$k]($value, @$v);
        }

        return $value;
    }
    //============================================================// clean
    public static function clean(string $parameter = null)
    {
        if (!empty($parameter)) {
            if (array_key_exists($parameter, mzParams::$params)) unset(mzParams::$params[$parameter]);
            if (array_key_exists($parameter, mzParams::$errors)) unset(mzParams::$errors[$parameter]);
        } else {
            mzParams::$params = [];
            mzParams::$errors = [];
        }
    }
}
