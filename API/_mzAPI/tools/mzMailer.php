<?php
/* 1.0.0 */
//====================================//
// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//
require_once("plugins/PHPMailer/Exception.php");
require_once("plugins/PHPMailer/PHPMailer.php");
require_once("plugins/PHPMailer/SMTP.php");
//====================================//
class mzMailer
{
    //====================================//
    public $PHPMailer;
    //====================================//
    public function __construct()
    { //array(status, results);
        try {
            $this->PHPMailer = new PHPMailer(true);
            return new mzRes(200);
        } catch (PDOException $e) {
            return new mzRes(500, "unable_to_init={$e->getMessage()}");
        }
    }
    //====================================//
    public function send(): mzRes
    { //array(status, results);
        try {
            $this->PHPMailer->send();
            return new mzRes(200);
        } catch (Exception $e) {
            return new mzRes(500, "email_not_sent={$this->PHPMailer->ErrorInfo}");
        }
    }
    //====================================//
}
