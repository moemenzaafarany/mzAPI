<hr id="top">

<ul>
  <li>
    <a href="#getting-started">Getting Started</a>
    <ul>
      <li>
        <a href="#configs">Configs</a>
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
    <a href="#core">Core</a>
  </li>
  <li>
    <a href="#tools">Tools</a>
    <ul>
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
* `API/.htaccess`   -> Apache configs file, Do Not Edit.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### Configs
Configs file `API/configs.php` contain some data like project name, databases
```php
<?php
// Project title
mzAPI::$TITLE = 'Project';
// Max connections
mzAPI::$MAX_CONNS_PER_HOUR = null;
mzAPI::$MAX_CONNS_PER_MIN = null;
mzAPI::$MAX_CONNS_PER_SEC = null;
// Tools
mzAPI::tools(['mzDatabase', 'mzParams']);
// Databases
mzAPI::DB(
	'main',
	new mzDatabase(
		'mysql',
		'localhost',
		'dbname',
		'username',
		'password',
		mzParams::headers('User-Timezone')
	)
);
```

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
* `_debug/` for using any api in debug mode.
* `_docs/` will be added soon.
* `_errors/` is used to display all php errors in log file.
* `_errors/clear` is used to empty the log file.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

## Core:
mzAPI is the core class of the api, and has many functions as follow:
- `mzAPI::DB(string $name, mzDatabase $database = null)`: ?mzDatabase | Used to add or get databases to be used in the handlers. <br>
  Add a db to be used in the handlers.
  ```php
  mzAPI::DB("main", new mzDatabase());
  ```
  Get db to be used in the handlers.
  ```php
  mzAPI::DB("main")->connect();
  ```
- `mzAPI::response(int $status = null, string $error = null, string $message = null, $data = null, $x = null)`: void | Used to convey data by handlers. <br>
  ```php
  mzAPI::response(200, null, 'success', ["data" => 1]);
  ```
- `mzAPI::tools(array $tools = null)`: void | Used to get mzTools for use in a handler. <br>
  Include a tool for use, and list of tools:
  ```php
  mzAPI::tools(['mzMailer']);
  ```
  * `mzDatabase`
  * `mzExcel`
  * `mzFiles`
  * `mzFirebase`
  * `mzFtp`
  * `mzMailer`
  * `mzParams`
  * `mzPayment`
  * `mzPdf`
  * `mzExternalScript`
- `mzAPI::includes(array $includes = null)`: void | Used include any file in the includes folder to a file in the handlers. <br>
  Include a file for use:
  ```php
  mzAPI::includes(['function', 'folder/function']);
  ```
- `new mzRes(int $status = null, string $error = null, string $message = null, $data = null)` | Used in most mz functions as return value. <br>
  Create a response
  ```php
  $r = new mzRes(200, null, 'success', ["data" => 1]);
  ```
  Change to mzAPI::response();
  ```php
  $r->response();
  ```
  
<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

## Tools:

### mzDatabase:
mzDatabase is the database connection class for mz, and is used as follow:
- `$db = new mzDatabase(String $database_type, String $database_host, String $database_name, String $database_user, String $database_pass, Int $timezoneInMinutes = null)`: void <br>
  Constructor which is used to add the database credentials.
- `$r = $db->connect()`: mzRes | connects with the database. <br>
  returns `$r->status == 200` on success or `$r->status != 200` on error.
  <br>
  
- `$r = $db->beginTransaction()`: bool | begins database transaction. <br>
  returns `$r = true` on success or `$r = false` on error.
  <br>
  
- `$r = $db->endTransaction(bool $rollback = false)`: bool | ends database transaction and commit/rollback changes. <br>
  returns `$r = true` on success or `$r = false` on error.
  <br>
  
- `$r = $db->lastInsertId()`: string | get last insert id of auto_increment from the database. <br>
  returns `$r = string` on success or `$r = null` on error.
  <br>
  
- `$r = $db->listTables()`: mzRes | lists the tables in the database. <br>
  returns `$r->status == 200`$`$r->data == []` on success or `$r->status != 200` on error.
  <br>
  
- `$r = $db->listKeys()`: mzRes | lists the primary/foreign keys in the database. <br>
  returns `$r->status == 200`&`$r->data == []` on success or `$r->status != 200` on error.
  <br>
  
- `$r = $db->listColumns(String $table)`: mzRes | lists columns of a table. <br>
  returns `$r->status == 200`&`$r->data == []` on success or `$r->status != 200` on error.
  <br>
  
- `$r = $db->selectVersion()`: mzRes | Gets the mysql/database version. <br>
  returns `$r->status == 200`&`$r->data == version` on success or `$r->status != 200` on error.
  <br>
  
- `$r = $db->selectTimestamp()`: mzRes | Gets the current timestamp based on the timezone of the connection. <br>
  returns `$r->status == 200`&`$r->data == timestamp` on success or `$r->status != 200` on error.
  <br>
  
- `$r = $db->select(String $table, String $columns = null, String $join = null, String $arguments = "0", array $argument_bindings = null)`: mzRes <br>
  select statment in the database. <br>
  returns `$r->status == 200`&`$r->data == []` on success or `$r->status != 200` on error.
  - `$columns` is put as `['key'=>'value']` where key is column name in table.
  - `$join` is put as `LEFT JOIN table ON column=column` where any arguments between `SELECT column` and `WHERE` is put.
  - `$arguments` is put as `1`|`id=?` where any arguments between after `WHERE` is put.
  - `$argument_bindings` is put as `['value']` where key is index which is put in where.
  <br>
  
- `$r = $db->insert(String $table, array $data)`: mzRes <br>
  insert statment in the database. <br>
  returns `$r->status == 200` on success or `$r->status != 200` on error. <br>
  - `$data` is put as `['key'=>'value']` where key is column name in table.
  <br>
  
- `$r = $db->update(String $table, array $data, String $arguments = "0", array $argument_bindings = null)`: mzRes <br>
  update statment in the database. <br>
  returns `$r->status == 200` on success or `$r->status != 200` on error. <br>
  - `$data` is put as `['key'=>'value']` where key is column name in table.
  - `$arguments` is put as `1`|`id=?` where any arguments between after `WHERE` is put.
  - `$argument_bindings` is put as `['value']` where key is index which is put in where.
  <br>
  
- `$r = $db->delete(String $table, String $arguments = "0", array $argument_bindings = null)`: mzRes <br>
  delete statment in the database. <br>
  returns `$r->status == 200` on success or `$r->status != 200` on error. <br>
  - `$arguments` is put as `1`|`id=?` where any arguments between after `WHERE` is put.
  - `$argument_bindings` is put as `['value']` where key is index which is put in where.
  <br>
  
- `$r = $db->execute(String $query, array $argument_bindings = null)`: mzRes <br>
  execute custom statment in the database. <br>
  returns `$r->status == 200` on success or `$r->status != 200` on error. <br>
  - `$argument_bindings` is put as `['value']` where key is index which is put in where.

<p align="right">(<a href="#top">back to top</a>)</p>
<hr>

### mzParams:
mzDatabase is the database connection class for mz, and is used as follow:
- `$db = new mzDatabase(String $database_type, String $database_host, String $database_name, String $database_user, String $database_pass, Int $timezoneInMinutes = null)`: void <br>
  Constructor which is used to add the database credentials.
- `$r = $db->connect()`: mzRes | connects with the database. <br>
  returns `$r->status == 200` on success or `$r->status != 200` on error.
  <br>
