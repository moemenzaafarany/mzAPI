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

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Media Folder
Media folder is where any files uploaded will be stored and accessed reside. <br>
All media files will be stored in `API/media/` and will be called as follow:
* Flat file `API/media/logo.png` => `API/_media/logo.png`.
* File within a folder `API/media/images/logo.png` => `API/_media/images/logo.png`.
<br>

In short, any file in media is can be accessed through `API/_media/{file location in media});`.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### URL keywords
Some keywords are reserved in mzAPI as follow: <br>
* `_media/` is used to access any file in media folder.
* `_errors/` is used to display all php errors in log file.
* `_errors/clear` is used to empty the log file.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

## Classes

### mzAPI:
mzAPI is the core class of the api, and has many functions as follow:
- `mzAPI::DB(string $name, mzDatabase $database = null)`: ?mzDatabase | Used to add or get databases to be used in the handlers. <br>
  Add a db to be used in the handlers.
  ```sh
  mzAPI::DB("main", new mzDatabase());
  ```
  Get db to be used in the handlers.
  ```sh
  mzAPI::DB("main")->connect();
  ```
- `mzAPI::response(int $status = null, string $error = null, string $message = null, $data = null, $x = null)`: void | Used to convey data by handlers. <br>
  ```sh
  mzAPI::response(200, null, 'success', ["data" => 1]);
  ```
- `mzAPI::tools(array $tools = null)`: void | Used to get mzTools for use in a handler. <br>
  Include a tool for use, and list of tools:
  ```sh
  mzAPI::tools(['mzMailer']);
  ```
  * `mzExcel`
  * `mzFiles`
  * `mzFirebase`
  * `mzFtp`
  * `mzMailer`
  * `mzPayment`
  * `mzPdf`
  * `mzExternalScript`
- `mzAPI::includes(array $includes = null)`: void | Used include any file in the includes folder to a file in the handlers. <br>
  Include a file for use:
  ```sh
  mzAPI::tools(['mzMailer']);
  ```
- `new mzRes(int $status = null, string $error = null, string $message = null, $data = null)` | Used in most mz functions as return value. <br>
  Create a response
  ```sh
  $r = new mzRes(200, null, 'success', ["data" => 1]);
  ```
  Change to mzAPI::response();
  ```sh
  $r->response();
  ```
  

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### mzDatabase:
mzDatabase is the database connection class for mz, and is used as follow:
- `$db = new mzDatabase(String $database_type, String $database_host, String $database_name, String $database_user, String $database_pass, Int $timezoneInMinutes = null)`<br>
  Constructor which is used to add the database credentials.
- `$db->connect()`: mzRes | connects with the database <br>
  Constructor which is used to add the database credentials.


<p align="right">(<a href="#top">back to top</a>)</p>
<hr>
