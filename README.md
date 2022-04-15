<hr id="top">

<ul>
  <li>
    <a href="#getting-started">Getting Started</a>
    <ul>
      <li>
        <a href="#configs">Configs</a>
      </li>
      <li>
        <a href="#apache-config">Apache Config</a>
      </li>
      <li>
        <a href="#handlers-folder">Handlers Folder</a>
      </li>
      <li>
        <a href="#includes-folder">Includes Folder</a>
      </li>
      <li>
        <a href="#media-folder">Media Folder</a>
      </li>
      <li>
        <a href="#url-keywords">URL keywords</a>
      </li>
    </ul>
  </li>
  <li>
    <a href="#classes">Classes</a>
    <ul>
      <li>
        <a href="#mzapi">mzAPI</a>
      </li>
      <li>
        <a href="#mzdatabase">mzDatabase</a>
      </li>
      <li>
        <a href="#mzparams">mzParams</a>
      </li>
    </ul>
  </li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

## Getting Started
mzAPI doesn't need installation just put the folder in you api folder as `API/_mzAPI` and run the `API/_mzAPI/index.php` file
and the script will generate any missing file/folder as follow:
* `API/handlers/`   -> Any php script the user will connect to is here.
* `API/includes/`   -> Any php script used by more than one handler is here.
* `API/media/`      -> Any files uploaded to be saved here.
* `API/configs.php` -> Php configs file, for project, databases, folders, etc.
* `API/.htaccess`   -> Apache configs file, Change as per needed,

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Configs
Configs file `API/configs.php` contain some data like project name, databases

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Apache Config
Apache config file `API/.htaccess` contain some php settings and an important file so the APIs coud run, edit with discretion.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Handlers Folder
Handlers folder is used as the main interface where all the apis/handlers reside. <br>
All handlers files will be stored in `API/handlers/` and will be called as follow:
* Flat file `API/handlers/example.php` => `API/example`.
* File within a folder `API/handlers/test/example.php` => `API/test/example`. 
<br>
In short, any php file in handlers is can be accessed through `API/{file location in handlers}` without the .php ext.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Includes Folder
Includes folder is where the repetitive functions that will be called by multiple handlers reside. <br>
All includes files will be stored in `API/includes/` and will be called as follow:
* Flat file `API/includes/example.php` => `mzAPI::includes(['example']);`.
* File within a folder `API/includes/test/example.php` => `mzAPI::includes(['test/example']);`.
<br>
In short, any php file in includes is can be accessed through `mzAPI::includes(['{file location in includes}']);` without the .php ext.

Apache config file `API/.htaccess` contain some php settings and an important file so the APIs coud run, edit with discretion.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Media Folder
Apache config file `API/.htaccess` contain some php settings and an important file so the APIs coud run, edit with discretion.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

## Classes

### EbTranslate:
  - EbTranslate.current : Locale?
  - EbTranslate.locales : List\<Locale\>
    ```sh
    localizationsDelegates: const [
      GlobalCupertinoLocalizations.delegate,
      GlobalMaterialLocalizations.delegate,
      GlobalWidgetsLocalizations.delegate,
    ],
    locale: EbTranslate.current,
    supportedLocales: EbTranslate.locales,
    ``` 
  - EbTranslate.init() : void
    ```sh
    void main() {
      WidgetsFlutterBinding.ensureInitialized();

      // init translation
      EbTranslate.init();

      // run app
      runApp(MyApp());
    }
    ``` 
  - EbTranslate.set(Locale locale) : void
    ```sh
    EbTranslate.set(Locale("en));
    ``` 
  - EbTranslate.get(String? key, {List\<String\>? filler, Locale? locale}) : String
    - key : String? | map keys in translation json
      ```sh
      EbTranslate.get("key");
      ```
    - filler : List\<String\>? | if translation has \<filler\> then is replace with array according to index
      ```sh
      EbTranslate.get("key-in-translation-json", filler: ["filler"]);
      ```
    - locale : Locale? | if translation is needed in a language other than the current
      ```sh
      EbTranslate.get("key-in-translation-json", locale: Locale("en"));
      ``` 

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### EbXhr:
  - EbXhr
    - new EbXhr(String method, String url, { Map<String, dynamic>? headers, Map<String, dynamic>? data) : Void
        ```sh
        EbXhr xhr = new EbXhr("POST", "https://api.com/", data: {"name": "John Smith", "file": FILE()});
        ```
    - EbXhr.send() : Future\<EbXhrReponse\>
        ```sh
        EbXhrReponse response = await xhr.send();
        ``` 
  - EbXhrReponse
    - EbXhrReponse.httpCode : int | Response Http Code
        ```sh
        response.httpCode = 200 | 400 | 500;
        ``` 
    - EbXhrReponse.error : String? | Response Error
        ```sh
        response.error = null | "string error";
        ``` 
    - EbXhrReponse.bodyText : String | Response Body
        ```sh
        response.bodyText = '{"data": "stuff"}';
        ``` 
    - EbXhrReponse.bodyJson : Map?
        ```sh
        response.bodyJson = null | {data: "stuff"};
        ``` 
  - ExabyteAPIResponse
    - new ExabyteAPIResponse(response) : Void | Our api response
        ```sh
        ExabyteAPIResponse exaRes = new ExabyteAPIResponse(response);
        ```
    - ExabyteAPIResponse.status : int? | Api Response status
        ```sh
        exaRes.status = 200 | 400 | 500;
        ``` 
    - ExabyteAPIResponse.statusCode : String? | Api Response status Text
        ```sh
        exaRes.statusCode = "OK" | "Bad Request" | "Internal Server Error";
        ``` 
    - ExabyteAPIResponse.error : String? | Api Response error
        ```sh
        exaRes.error = null | "error";
        ``` 
    - ExabyteAPIResponse.message : String? | Api Response message
        ```sh
        exaRes.message = null | "message";
        ``` 
    - ExabyteAPIResponse.data : dynamic? | Api Response data
        ```sh
        exaRes.data = null | ["data'] | {"data": "stuff"};
        ``` 
    - ExabyteAPIResponse.run({Function? s200, Function? s400, Function? s401, Function? s403, Function? s404, Function? s500, Function? sdefault}) : Void | Run Functions for each status
        ```sh
        exaRes.run(
          s200: ()=>print(200),
          s400: ()=>print(400),
          sdefault: ()=>print('else'),
        );
        ``` 

<p align="right">(<a href="#mzAPI">back to top</a>)</p>
<hr>

### EbUI:
  - EbUI.theme : ThemeMode | Current theme mode
      ```sh
      EbUI.theme = ThemeMode.system | ThemeMode.light  | ThemeMode.dark;
      ```
  - EbUI.fontSize : double | set fontsize for project
      ```sh
      EbUI.fontSize = 16;
      ```
  - EbUI.iconSize : double | set icon size for project
      ```sh
      EbUI.iconSize = 20;
      ```
  - EbUI.fontFamily : ThemeMode | set font family for project
      ```sh
      EbUI.fontFamily = "Roboto";
      ```
  - EbUI.screen(BuildContext context, double? xs, double? sm, double? md, double? lg, double? xl) : double | get double per screen size
      ```sh
      double grid = EbUI.screen(context, 1, 2, 3, 4, 5);
      ```
  - EbUI.themeData() : ThemeData | get current themedata with current colors and params
      ```sh
      EbUI.themeData();
      ```
  - EbUI.colors : EbUIColors  | Current ui colors
    - EbUI.colors.{any param} : Color | colors in ui

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>
