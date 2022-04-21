<?php
/* 1.0.0 */
class mzExternalScript
{
    //===============================================================================//
    public ?string $script_url;

    //===============================================================================//
    public function __construct(String $script_url)
    {
        $this->script_url = $script_url;
    }

    //===============================================================================//
    private function _cUrl(string $method, string $url, array $headers = null, array $data = null)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($headers)) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl) ?: null;
            curl_close($curl);
            return new mzRes($httpCode, $error, null, $response);
        } catch (Exception $e) {
            return new mzRes(500, $e);
        }
    }

    //===============================================================================//
    public function check(): mzRes
    {
        return $this->_cUrl("POST", $this->script_url, null, ['_run' => 'echo "hi";']);
    }

    //===============================================================================//
    public function run(array $tools = null, array $includes = null, array $data = null): mzRes
    {
        $data["_run"] = "";
        // mzAPI
        $data["_run"] .= ltrim(file_get_contents(mzAPI::CLASSES_DIR . "mzRES.php"), "<?php");
        $data["_run"] .= ltrim(file_get_contents(mzAPI::CLASSES_DIR . "mzAPI.php"), "<?php");
        // add tools 
        if (!empty($tools)) {
            foreach ($tools as $tool) {
                $tool = mzAPI::TOOLS_DIR . $tool . ".php";
                if (is_file($tool)) {
                    $data["_run"] .= ltrim(file_get_contents($tool), "<?php");
                }
            }
        }
        // add includes 
        if (!empty($includes)) {
            foreach ($includes as $include) {
                $include = mzAPI::INCLUDES_DIR . $include . ".php";
                if (is_file($include)) {
                    $data["_run"] .= ltrim(file_get_contents($include), "<?php");
                }
            }
        }
        //
        return $this->_cUrl("POST", $this->script_url, null, $data);
    }
    //===============================================================================//
}
