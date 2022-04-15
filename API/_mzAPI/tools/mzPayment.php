<?php
/* 1.0.0 */
//===============================================================================//
// eshopping
require_once("plugins/eshopping-EGY_national_bank_of_egypt/SME.php");
//===============================================================================//
class mzPayment
{
  //===============================================================================//
  private static $EGY_BANK_national_bank_of_egypt_params = [
    "actions" => ["payment", "refund", "unmatched_refund", "preauth", "capture", "reversal"],
    "type" => ["callcentre", "cardpresent", "ecommerce", "internet", "ivr", "mailorder", "telephoneorder"],
    "sub-type" => ["single", "recurring"],
    "tokenisations" => [0 => "Default", 1 => "Do not tokenise", 2 => "Tokenise if customer opts in", 3 => "Always tokenise"],
    "currencies" => ["DZD" => "Algerian Dinar", "ARS" => "Argentine Peso", "AUD" => "Australian Dollar", "BSD" => "Bahamian Dollar", "BHD" => "Bahraini Dinar", "BDT" => "Bangladeshi Taka", "AMD" => "Armenian Dram", "BBD" => "Barbados Dollar", "BMD" => "Bermudian Dollar", "BTN" => "Bhutanese Ngultrum", "BOB" => "Boliviano", "BWP" => "Botswana Pula", "BZD" => "Belize Dollar", "SBD" => "Solomon Islands Dollar", "BND" => "Brunei Dollar", "MMK" => "Myanmar Kyat", "BIF" => "Burundi Franc", "KHR" => "Cambodian Riel", "CAD" => "Canadian Dollar", "CVE" => "Cape Verde Escudo", "KYD" => "Cayman Islands Dollar", "LKR" => "Sri Lanka Rupee", "CLP" => "Chilean Peso", "CNY" => "Yuan Renminbi", "KMF" => "Comoros Franc", "CRC" => "Costa Rican Colon", "HRK" => "Croatian Kuna", "CUP" => "Cuban Peso", "CZK" => "Czech Koruna", "DKK" => "Danish Krone", "DOP" => "Dominican Peso", "ETB" => "Ethiopian Birr", "ERN" => "Eritrean Nakfa", "FKP" => "Falkland Islands Pound", "FJD" => "Fiji Dollar", "DJF" => "Djibouti Franc", "GMD" => "Gambian Dalasi", "GIP" => "Gibraltar Pound", "GTQ" => "Guatemalan Quetzal", "GNF" => "Guinea Franc", "GYD" => "Guyana Dollar", "HTG" => "Haitian Gourde", "HNL" => "Honduran Lempira", "HKD" => "Hong Kong Dollar", "HUF" => "Hungarian Forint", "ISK" => "Iceland Krona", "INR" => "Indian Rupee", "IDR" => "Indonesian Rupiah", "IRR" => "Iranian Rial", "IQD" => "Iraqi Dinar", "ILS" => "Israeli New Shekel", "JMD" => "Jamaican Dollar", "JPY" => "Japanese Yen", "KZT" => "Kazakhstan Tenge", "JOD" => "Jordanian Dinar", "KES" => "Kenyan Shilling", "KPW" => "North Korean Won", "KRW" => "Korean Won", "KWD" => "Kuwaiti Dinar", "KGS" => "Kyrgyzstani Som", "LAK" => "Lao Kip", "LBP" => "Lebanese Pound", "LSL" => "Lesotho Loti", "LRD" => "Liberian Dollar", "LYD" => "Libyan Dinar", "MOP" => "Macau Pataca", "MWK" => "Malawi Kwacha", "MYR" => "Malaysian Ringgit", "MVR" => "Maldive Rufiyaa", "MRO" => "Mauritanian Ouguiya", "MUR" => "Mauritius Rupee", "MXN" => "Mexican Nuevo Peso", "MNT" => "Mongolian Tugrik", "MDL" => "Moldovan Leu", "MAD" => "Moroccan Dirham", "OMR" => "Omani Rial", "NAD" => "Namibian Dollar", "NPR" => "Nepalese Rupee", "ANG" => "Netherlands Antillean guilder", "AWG" => "Aruban Guilder", "VUV" => "Vanuatu Vatu", "NZD" => "New Zealand Dollar", "NIO" => "Nicaraguan Cordoba Oro", "NGN" => "Nigerian Naira", "NOK" => "Norwegian Krone", "PKR" => "Pakistan Rupee", "PAB" => "Panamanian Balboa", "PGK" => "Papua New Guinea Kina", "PYG" => "Paraguay Guarani", "PEN" => "Peruvian Nuevo Sol", "PHP" => "Philippine Peso", "QAR" => "Qatari Rial", "RUB" => "Russian Ruble", "RWF" => "Rwanda Franc", "SHP" => "St. Helena Pound", "STD" => "Dobra", "SAR" => "Saudi Riyal", "SCR" => "Seychelles Rupee", "SLL" => "Sierra Leone Leone", "SGD" => "Singapore Dollar", "VND" => "Vietnamese Dong", "SOS" => "Somali Shilling", "ZAR" => "South African Rand", "SSP" => "South Sudan Pound", "SZL" => "Swaziland Lilangeni", "SEK" => "Swedish Krona", "CHF" => "Swiss Franc", "SYP" => "Syrian Pound", "THB" => "Thai Baht", "TOP" => "Tongan Paanga", "TTD" => "Trinidad and Tobago Dollar", "AED" => "Arab Emirates Dirham", "TND" => "Tunisian Dollar", "UGX" => "Uganda Shilling", "MKD" => "Denar", "EGP" => "Egyptian Pound", "GBP" => "Pound sterling", "TZS" => "Tanzanian Shilling", "USD" => "US Dollar", "UYU" => "Uruguayan Peso", "UZS" => "Uzbekistan Sum", "WST" => "Samoan Tala", "YER" => "Yemeni Rial", "TWD" => "New Taiwan dollar", "CUC" => "Cuban convertible peso", "ZWL" => "Zimbabwean Dollar", "TMT" => "Turkmenistani manat", "GHS" => "Ghanaian cedi", "VEF" => "Venezuelan bolívar", "SDG" => "Sudanese pound", "RSD" => "Serbian dinar", "MZN" => "Mozambican metical", "AZN" => "Azerbaijani manat", "RON" => "Romanian new leu", "TRY" => "Turkish lira", "XAF" => "CFA franc BEAC", "XCD" => "East Caribbean dollar", "XOF" => "Communauté Financière Africaine (BCEAO) Franc", "XPF" => "Comptoirs Franç ais du Pacifique (CFP) Franc", "XDR" => "International Monetary Fund (IMF) Special Drawing Rights", "ZMW" => "Zambia Kwacha", "SRD" => "Suriname Dollar", "MGA" => "Malagasy Ariary", "AFN" => "Afghan Afghani", "TJS" => "Tajikistani Somoni", "AOA" => "Angolan Kwanza", "BYR" => "Belarusian Ruble", "BGN" => "Bulgarian lev", "CDF" => "Congolese Franc", "BAM" => "Bosnia and Herzegovina Convertible Marka", "EUR" => "Euro", "UAH" => "Ukrainian Hryvnia", "GEL" => "Georgian Lari", "PLN" => "Poland Zloty", "BRL" => "Brazil Real"],
  ];
  //===============================================================================//
  public function EGY_BANK_national_bank_of_egypt(bool $TestMode = true, string $username, string $merchantnumber, string $password, string $Action, float $Amount, string $CardNumber, string $Cvn, string $ExpiryDate, string $Crn1, string $Currency, string $MerchantReference, string $OriginalTxnNumber, string $EmailAddress, string $TokenisationMode, string $SubType, string $Type): object
  { //array(status, results);
    try {
      SME\URLDirectory::setBaseURL("reserved", "https://eshopping.nbe.com.eg/webapi/v2");
      $credentials = new SME\Credentials("APIUserName", "Passw0rd", "12345678", SME\Mode::Live);

      $txn = new SME\Transaction();
      $cardDetails = new SME\CardDetails();
      $order = new SME\Order();
      $shippingAddress = new SME\OrderAddress();
      $billingAddress = new SME\OrderAddress();
      $address = new SME\Address();
      $customer = new SME\Customer();
      $personalDetails = new SME\PersonalDetails();
      $contactDetails = new SME\ContactDetails();
      $order_item_1 = new SME\OrderItem();
      $order_recipient_1 = new SME\OrderRecipient();
      $fraudScreening = new SME\FraudScreeningRequest();

      $txn->setAction(SME\Actions::Payment);
      $txn->setCredentials($credentials);
      $txn->setAmount(20000);
      $txn->setCurrency("AUD");
      $txn->setMerchantReference("Merchant Reference");
      $txn->setCrn1("My Customer Reference");
      $txn->setCrn2("Medium");
      $txn->setCrn3("Large");
      $txn->setStoreCard(FALSE);
      $txn->setSubType("single");
      $txn->setType(SME\TransactionType::Internet);

      $cardDetails->setCardHolderName("MR C CARDHOLDER");
      $cardDetails->setCardNumber("4444333322221111");
      $cardDetails->setCVN("678");
      $cardDetails->setExpiryDate("0517");

      $txn->setCardDetails($cardDetails);

      $address->setAddressLine1("123 Fake Street");
      $address->setCity("Melbourne");
      $address->setCountryCode("AUS");
      $address->setPostCode("3000");
      $address->setState("Vic");

      $contactDetails->setEmailAddress("example@email.com");

      $personalDetails->setDateOfBirth("1900-01-01");
      $personalDetails->setFirstName("John");
      $personalDetails->setLastName("Smith");
      $personalDetails->setSalutation("Mr");

      $billingAddress->setAddress($address);
      $billingAddress->setContactDetails($contactDetails);
      $billingAddress->setPersonalDetails($personalDetails);

      $shippingAddress->setAddress($address);
      $shippingAddress->setContactDetails($contactDetails);
      $shippingAddress->setPersonalDetails($personalDetails);

      $order_item_1->setDescription("an item");
      $order_item_1->setQuantity(1);
      $order_item_1->setUnitPrice(1000);

      $orderItems = array($order_item_1);

      $order_recipient_1->setAddress($address);
      $order_recipient_1->setContactDetails($contactDetails);
      $order_recipient_1->setPersonalDetails($personalDetails);

      $orderRecipients = array($order_recipient_1);

      $order->setBillingAddress($billingAddress);
      $order->setOrderItems($orderItems);
      $order->setOrderRecipients($orderRecipients);
      $order->setShippingAddress($shippingAddress);
      $order->setShippingMethod("boat");

      $txn->setOrder($order);

      $customer->setCustomerNumber("1234");
      $customer->setAddress($address);
      $customer->setExistingCustomer(false);
      $customer->setContactDetails($contactDetails);
      $customer->setPersonalDetails($personalDetails);
      $customer->setCustomerNumber("1");
      $customer->setDaysOnFile(1);

      $txn->setCustomer($customer);

      $fraudScreening->setPerformFraudScreening(true);
      $fraudScreening->setDeviceFingerprint("0400l1oURA1kJHkN<1900 characters removed>+ZKFOkdULYCXsUu0Oxk=");

      $txn->setFraudScreeningRequest($fraudScreening);

      $txn->setTokenisationMode(3);
      $txn->setTimeout(93121);

      $response = $txn->submit();

      return mzAPI::return(200, null, null, $response);
    } catch (Exception $e) {
      return mzAPI::return(500, "payment_failed={$this->PHPMailer->ErrorInfo}");
    }
  }
  //===============================================================================//
  public function EGY_BANK_national_bank_of_egypt2(bool $TestMode = true, string $username, string $merchantnumber, string $password, string $Action, float $Amount, string $CardNumber, string $Cvn, string $ExpiryDate, string $Crn1, string $Currency, string $MerchantReference, string $OriginalTxnNumber, string $EmailAddress, string $TokenisationMode, string $SubType, string $Type): object
  { //array(status, results);

    //
    $URL = "https://eshopping.nbe.com.eg/webapi/v2/txns/";
    $POST = true;
    $HEADERS = [
      "Authorization" => base64_encode("$username|$merchantnumber:$password"),
    ];
    $PARAMS = [
      "TxnReq" => [
        "TestMode" => $TestMode,
        "Action" => $Action,
        "Amount" => $Amount,
        //"AmountOriginal" => 19800,
        //"AmountSurcharge" => 100,
        //"BillerCode" => null,
        "CardDetails" => [
          "CardNumber" => $CardNumber,
          "Cvn" => $Cvn,
          "ExpiryDate" => $ExpiryDate
        ],
        "Crn1" => $Crn1,
        //"Crn2" => "test crn2",
        //"Crn3" => "test crn3",
        "Currency" => $Currency,
        "MerchantReference" => $MerchantReference, //partner
        /*
        "Customer" => [
          "Address" => [
            "AddressLine1" => "123 Fake Street",
            "AddressLine2" => "",
            "AddressLine3" => "",
            "City" => "Melbourne",
            "CountryCode" => "AUS",
            "PostCode" => "3000",
            "State" => "VIC"
          ],
          "ContactDetails" => [
            "EmailAddress" => "john.smith@email.com",
            "FaxNumber" => "",
            "HomePhoneNumber" => "",
            "MobilePhoneNumber" => "",
            "WorkPhoneNumber" => ""
          ],
          "CustomerNumber" => "1234",
          "PersonalDetails" => [
            "DateOfBirth" => "",
            "FirstName" => "John",
            "LastName" => "Smith",
            "MiddleName" => "",
            "Salutation" => "Mr"
          ],
          "DaysOnFile" => 23,
          "IsExistingCustomer" => true
        ],
        "Order" => [
          "BillingAddress" => [
            "Address" => [
              "AddressLine1" => "",
              "AddressLine2" => "",
              "AddressLine3" => "",
              "City" => "",
              "CountryCode" => "",
              "PostCode" => "",
              "State" => ""
            ],
            "ContactDetails" => [
              "EmailAddress" => "",
              "FaxNumber" => "",
              "HomePhoneNumber" => "",
              "MobilePhoneNumber" => "",
              "WorkPhoneNumber" => ""
            ],
            "PersonalDetails" => [
              "DateOfBirth" => "",
              "FirstName" => "",
              "LastName" => "",
              "MiddleName" => "",
              "Salutation" => ""
            ]
          ],
          "OrderItems" => [[
            "Comments" => "",
            "Description" => "",
            "GiftMessage" => "",
            "PartNumber" => "",
            "ProductCode" => "",
            "Quantity" => 1,
            "SKU" => "",
            "ShippingMethod" => "",
            "ShippingNumber" => "",
            "UnitPrice" => 100
          ], [
            "Comments" => "",
            "Description" => "",
            "GiftMessage" => "",
            "PartNumber" => "",
            "ProductCode" => "",
            "Quantity" => 1,
            "SKU" => "",
            "ShippingMethod" => "",
            "ShippingNumber" => "",
            "UnitPrice" => 100
          ]],
          "ShippingAddress" => [
            "Address" => [
              "AddressLine1" => "",
              "AddressLine2" => "",
              "AddressLine3" => "",
              "City" => "",
              "CountryCode" => "",
              "PostCode" => "",
              "State" => ""
            ],
            "ContactDetails" => [
              "EmailAddress" => "",
              "FaxNumber" => "",
              "HomePhoneNumber" => "",
              "MobilePhoneNumber" => "",
              "WorkPhoneNumber" => ""
            ],
            "PersonalDetails" => [
              "DateOfBirth" => "",
              "FirstName" => "",
              "LastName" => "",
              "MiddleName" => "",
              "Salutation" => ""
            ]
          ],
          "ShippingMethod" =>  "",
          "OrderRecipients" => [
            [
              "PersonalDetails" => [
                "DateOfBirth" => "",
                "FirstName" => "",
                "LastName" => "",
                "MiddleName" => "",
                "Salutation" => ""
              ],
              "ContactDetails" => [
                "EmailAddress" => "",
                "FaxNumber" => "",
                "HomePhoneNumber" => "",
                "MobilePhoneNumber" => "",
                "WorkPhoneNumber" => ""
              ],
              "Address" => [
                "AddressLine1" => "",
                "AddressLine2" => "",
                "AddressLine3" => "",
                "City" => "",
                "CountryCode" => "",
                "PostCode" => "",
                "State" => ""
              ]
            ],
            [
              "PersonalDetails" => [
                "DateOfBirth" => "",
                "FirstName" => "",
                "LastName" => "",
                "MiddleName" => "",
                "Salutation" => ""
              ],
              "ContactDetails" => [
                "EmailAddress" => "",
                "FaxNumber" => "",
                "HomePhoneNumber" => "",
                "MobilePhoneNumber" => "",
                "WorkPhoneNumber" => ""
              ],
              "Address" => [
                "AddressLine1" => "",
                "AddressLine2" => "",
                "AddressLine3" => "",
                "City" => "",
                "CountryCode" => "",
                "PostCode" => "",
                "State" => ""
              ]
            ],
          ],
        ],
        */
        "OriginalTxnNumber" => $OriginalTxnNumber,
        "EmailAddress" => $EmailAddress,
        "TokenisationMode" => $TokenisationMode,
        "SubType" => $SubType,
        "Type" => $Type,
        "StoreCard" => false,
        /*
        "FraudScreeningRequest" => [
          "PerformFraudScreening" => "true",
          "DeviceFingerprint" => "0400l1oURA1kJHkN<1900 characters removed>+ZKFOkdULYCXsUu0Oxk="
        ]*/
      ]
    ];
    try {
    } catch (Exception $e) {
      return mzAPI::return(200, "email_not_sent=" . $this->PHPMailer->ErrorInfo);
    }
  }
  //===============================================================================//
  public function cURL(string $url, $idk): object
  { //array(status, results);
    $r = [];

    $ch = curl_init("http://www.example.com/");
    $fp = fopen("example_homepage.txt", "w");

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_exec($ch);
    if (curl_error($ch)) {
      fwrite($fp, curl_error($ch));
    }
    curl_close($ch);
    fclose($fp);

    $params = ['name' => 'John', 'surname' => 'Doe', 'age' => 36];
    $defaults = array(
      CURLOPT_URL => 'http://myremoteservice/',
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $params,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $defaults);

    return (object) $r;
  }
  //===============================================================================//
}
