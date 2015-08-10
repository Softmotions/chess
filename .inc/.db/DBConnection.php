<?php

require_once( "./.inc/.db/DataBaseException.php" );

/**
 * Объект-обёртка таблицы данных для извлечения значений
 */
final class DBResultSet {
  private $statement;

  private $datatype;
  private $datainfo;
  private $dataname;
  private $data;

  /**
	 * Создание и инициализация
	 * @param mysqli_stmt $statement
	 */
  public function __construct( $statement ) {
    $this->statement = $statement;

    $metadata = $this->statement->result_metadata();
    $fields = $metadata->fetch_fields();

    $this->datatype = array();
    $this->datainfo = array();
    $this->dataname = array();
    $this->data = array();

    if( count( $fields ) > 0 ) {
      $delemiter = "";
      $bindcode = "\$this->statement->bind_result( ";
      foreach ( $fields as $fieldIndex => $fieldInfo ) {
        $this->data[ $fieldIndex ] = "";

        $this->datainfo[ $fieldIndex ] = $fieldIndex;
        $this->datainfo[ $fieldInfo->name ] = $fieldIndex;

        $this->dataname[ $fieldIndex ] = $fieldInfo->name;

        switch( $fieldInfo->type ) {
          case MYSQLI_TYPE_BIT:
          case MYSQLI_TYPE_DECIMAL:
            //					case MYSQLI_TYPE_NEWDECIMAL:
          case MYSQLI_TYPE_TINY:
          case MYSQLI_TYPE_SHORT:
          case MYSQLI_TYPE_LONG:
          case MYSQLI_TYPE_LONGLONG:
          case MYSQLI_TYPE_INT24:
            $this->datatype[ $fieldIndex ] = 'i';
            break;

          case MYSQLI_TYPE_FLOAT:
          case MYSQLI_TYPE_DOUBLE:
            $this->datatype[ $fieldIndex ] = 'd';
            break;

          case MYSQLI_TYPE_VAR_STRING:
          case MYSQLI_TYPE_STRING:
            $this->datatype[ $fieldIndex ] = 's';
            break;

          case MYSQLI_TYPE_TINY_BLOB:
          case MYSQLI_TYPE_MEDIUM_BLOB:
          case MYSQLI_TYPE_LONG_BLOB:
          case MYSQLI_TYPE_BLOB:
            $this->datatype[ $fieldIndex ] = 'S';
            break;

          case MYSQLI_TYPE_NULL:
          case MYSQLI_TYPE_TIMESTAMP:
          case MYSQLI_TYPE_DATE:
          case MYSQLI_TYPE_TIME:
          case MYSQLI_TYPE_DATETIME:
          case MYSQLI_TYPE_YEAR:
          case MYSQLI_TYPE_NEWDATE:
          case MYSQLI_TYPE_ENUM:
          case MYSQLI_TYPE_SET:
          case MYSQLI_TYPE_GEOMETRY:
          default:
            throw new DataBaseException( "DBResultSet.<construct>()", "Not supported column type for field: '".$fieldInfo->name."' (type: ".$fieldInfo->type.")", -5 );
            break;

        }

        $bindcode .= $delemiter."\$this->data[ $fieldIndex ]";
        $delemiter = ", ";
      }
      $bindcode .= " );";

      eval( $bindcode );
    }
  }

  /**
	 * Возвращает целое число колонки из текущей строки
	 * @param mixed $id индекс или название колонки
	 * @param int $default дефолтное значение
	 * @return int целое число колонки из текущей строки
	 */
  public function getInteger( $id, $default = null ) {
    if( !isset( $this->datainfo[ $id ] ) ) {
      throw new DataBaseException( "DBResultSet.getInteger()", "Column '$id' not found.", -1 );
    }

    $index = $this->datainfo[ $id ];
    if( 'i' != $this->datatype[ $index ] ) {
      throw new DataBaseException( "DBResult.getInteger()", "Column type is not numeric.", -6 );
    }

    if( !isset( $this->data[ $index ] ) || $this->data[ $index ] === null ) {
      return $default;
    }
    return intval( $this->data[ $index ] );
  }

  /**
	 * Возвращает вещественное значение колонки из текущей строки
	 * @param mixed $id индекс или название колонки
	 * @param double $default дефолтное значение
	 * @return double вещественное значение колонки из текущей строки
	 */
  public function getDouble( $id, $default = null ) {
    if( !isset( $this->datainfo[ $id ] ) ) {
      throw new DataBaseException( "DBResultSet.getDouble()", "Column '$id' not found.", -1 );
    }

    $index = $this->datainfo[ $id ];
    if( 'd' != $this->datatype[ $index ] ) {
      throw new DataBaseException( "DBResult.getDouble()", "Column type is not float.", -6 );
    }

    if( !isset( $this->data[ $index ] ) || $this->data[ $index ] === null ) {
      return $default;
    }
    return doubleval( $this->data[ $index ] );
  }

  /**
	 * Возвращает строковое значение колонки из текущей строки
	 * @param mixed $id индекс или название колонки
	 * @param string $default дефолтное значение
	 * @return string строковое значение колонки из текущей строки
	 */
  public function getString( $id, $default = null ) {
    if( !isset( $this->datainfo[ $id ] ) ) {
      throw new DataBaseException( "DBResultSet.getString()", "Column '$id' not found.", -1 );
    }

    $index = $this->datainfo[ $id ];
    if( 's' != $this->datatype[ $index ] && 'S' != $this->datatype[ $index ] ) {
      throw new DataBaseException( "DBResult.getString()", "Column type is not string.", -6 );
    }

    if( !isset( $this->data[ $index ] ) || $this->data[ $index ] === null ) {
      return $default;
    }
    return strval( $this->data[ $this->datainfo[ $id ] ] );
  }

  /**
	 * Проверка на null
	 * @param mixed $id индекс или название колонки
	 * @return bool
	 */
  public function isNull( $id ) {
    $index = $this->datainfo[ $id ];
    return ( !isset( $this->data[ $index ] ) || $this->data[ $index ] === null );
  }

  /**
	 * Возвращает данные строки таблицы в виде ассоциированного массива
	 * @return array массив значений
	 * @throws DataBaseException в случае обнаружения 'незарегистрированной' колонки
	 */
  public function dataRow() {
    $result = array();
    foreach( $this->data as $index => $dvalue ) {
      if( !isset( $this->data[ $index ] ) || $this->data[ $index ] === null ) {
        $value = null;
      } else {
        switch( $this->datatype[ $index ] ) {
          case 'i':
            $value = intval( $dvalue );
            break;

          case 'd':
            $value = doubleval( $dvalue );
            break;

          case 'S':
          case 's':
            $value = strval( $dvalue );
            break;

          default:
            throw new DataBaseException( "DBResultSet.dataRow()", "Unexcpected column type.", -7 );

        }
      }

      $result[ $this->dataname[ $index ] ] = $value;
    }

    return $result;
  }

  /**
	 * Переходит к следующей строке в таблице результатов. Возвращает флаг наличия этой строки
	 * @return bool флаг наличия следующего элемента
	 */
  public function next() {
    $status = $this->statement->fetch();

    if( TRUE == $status ) {
      return true;
    } elseif ( NULL == $status ) {
      return false;
    } else {
      throw new DataBaseException( "DBResultSet.next()", $this->statement->error, $this->statement->errno );
    }
  }

  /**
	 * Закрывает ResultSet (realy, do nothing)
	 */
  public function close() {
  }
}

/**
 * Объект-обёртка для выполнения запросов.
 */
final class DBStatement {
  private $statement;

  private $paraminfos;
  private $params;

  /**
	 * Создание и инициализация
	 * @param mysqli_stmt $statement
	 */
  public function __construct( $statement ) {
    $this->statement = $statement;

    $this->paraminfos = array();
    $this->params = array();
  }

  /**
	 * Выполнение запроса получения данных
	 * @return DBResultSet объект-обёртка для получения данных
	 */
  public function execute() {
    $this->bindParams();
    if( !$this->statement->execute() ) {
      throw new DataBaseException( "DBStatement.execute()", $this->statement->error, $this->statement->errno );
    }

    return new DBResultSet( $this->statement );
  }

  /**
	 * Выполнение запроса на модификацию данных в БД (INSERT/REPLACE/DELETE/UPDATE)
	 * @throws DataBaseException в случае каакий-либо ошибки в обработке запроса
	 */
  public function executeUpdate() {
    $this->bindParams();
    if( !$this->statement->execute() ) {
      throw new DataBaseException( "DBStatement.execute()", $this->statement->error, $this->statement->errno );
    }
  }

  /**
	 * Устанавливает целочисленный параметр запроса по индексу
	 * @param int $id индекс
	 * @param int $data число для запроса
	 */
  public function setInteger( $id, $data ) {
    $index = intval( $id );
    if( 0 > $index || $index >= $this->statement->param_count ) {
      throw new DataBaseException( "DBStatement.setInteger()", "Unexpected parameter '$index'.", -3 );
    }

    $this->paraminfos[ $index ] = 'i';
    $this->params[ $index ] = intval( $data );
  }

  /**
	 * Устанавливает вещественный параметр запроса по индексу
	 * @param int $id индекс
	 * @param double $data число для запроса
	 */
  public function setDouble( $id, $data ) {
    $index = intval( $id );
    if( 0 > $index || $index >= $this->statement->param_count ) {
      throw new DataBaseException( "DBStatement.setDouble()", "Unexpected parameter '$index'.", -3 );
    }

    $this->paraminfos[ $index ] = 'd';
    $this->params[ $index ] = doubleval( $data );
  }

  /**
	 * Устанавливает строковой параметр запроса по индексу
	 * @param int $id индекс
	 * @param string $data строка для запроса
	 */
  public function setString( $id, $data ) {
    $index = intval( $id );
    if( 0 > $index || $index >= $this->statement->param_count ) {
      throw new DataBaseException( "DBStatement.setString()", "Unexpected parameter '$index'.", -3 );
    }

    $this->paraminfos[ $index ] = 's';
    $this->params[ $index ] = strval( $data );
  }

  /**
	 * Закрывает объект-statement для обработки запросов
	 */
  public function close() {
    $this->statement->close();
  }

  /**
	 * Инициализирует statement для выполнения запроса
	 */
  private function bindParams( ) {
    if( $this->statement->param_count < 1 ) {
      return;
    }

    $index = -1;

    $bindtype = "";
    $binddata = "";
    $delemiter = "";

    while( ++$index < $this->statement->param_count ) {
      if( !isset( $this->paraminfos[ $index ] ) ) {
        throw new DataBaseException( "DBStatement.<bindParams>()", "Absent parametr '$index'.", -2 );
      }
      $bindtype .= $this->paraminfos[ $index ];
      $binddata .= $delemiter."\$this->params[ $index ]";
      $delemiter = ", ";
    }
    $bindcode = "\$this->statement->bind_param( \"".$bindtype."\", ".$binddata." );";
    eval( $bindcode );
  }
}

/**
 * Объект-обёртка соединения с БД
 */
final class DBConnection {
  private static $instance;

  /**
   * @return DBConnection
   */
  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new DBConnection();
    }

    return self::$instance;
  }

  private $connection;

  /**
	 * Создание и инициализация (из конфигурационного файла)
	 * @throws DataBaseException в случае ошибки создания и инициализации соединения с сервером БД
	 */
  protected function __construct() {
    if( defined( "CFG_DB_PASSWORD" ) ) {
      $this->connection = new mysqli( CFG_DB_SERVER, CFG_DB_LOGIN, CFG_DB_PASSWORD );
    } else {
      $this->connection = new mysqli( CFG_DB_SERVER, CFG_DB_LOGIN );
    }

    if( mysqli_connect_errno() ) {
      throw new DataBaseException( "DBConnection.<construct>()", mysqli_connect_error(), mysqli_connect_errno() );
    }

    if( !$this->connection->select_db( CFG_DB_DATABASE ) ) throw new DataBaseException( "DBConnection.<construct>()", $this->connection->error, $this->connection->errno );
    if( !$this->connection->real_query( "set character_set_connection='utf8';" ) ) throw new DataBaseException( "DBConnection.<construct>()", $this->connection->error, $this->connection->errno );
    if( !$this->connection->real_query( "set character_set_client='utf8';" ) ) throw new DataBaseException( "DBConnection.<construct>()", $this->connection->error, $this->connection->errno );
    if( !$this->connection->real_query( "set character_set_results='utf8';" ) ) throw new DataBaseException( "DBConnection.<construct>()", $this->connection->error, $this->connection->errno );
    //		if( !$this->connection->set_charset( "utf8" ) ) throw new DataBaseException( "DBConnection.<construct>()", $this->connection->error, $this->connection->errno );
    if( !$this->connection->autocommit( false ) ) throw new DataBaseException( "DBConnection.<construct>()", $this->connection->error, $this->connection->errno );
  }

  /**
	 * Выполняет прямой запрос на изменение данных в БД
	 * @param string $query запрос
	 * @throws DataBaseException если что-то не получилось
	 */
  public function executeUpdate( $query ) {
    if( !$this->connection->query( $query ) ) {
      throw new DataBaseException( "DBConnection.executeUpdate()", $this->connection->error, $this->connection->errno );
    }
  }

  /**
   * Returns the ID generated by a query on a table with a column having the AUTO_INCREMENT attribute.
   * If the last query wasn't an INSERT or UPDATE statement or
   * if the modified table does not have a column with the AUTO_INCREMENT attribute,
   * this function will return zero
   * @return mixed
   */
  public function getInsertedId() {
    return $this->connection->insert_id;
  }

  /**
	 * Выполнение прямого запроса к БД на получение данных (in this version do nothing)
	 * @param string $query запрос
	 * @return DBResultSet обёртка для получния данных
	 * @throws DataBaseException если что-то не получилось
	 * @deprecated
	 */
  public function execute( $query ) {
    throw new DataBaseException( "DBConnection.execute()", "Method in not supported now!", -4 );
  }

  /**
	 * Подготовка прекомпилированного запроса
	 * @param string $query запрос
   *
	 * @return DBStatement прекомпилированный запрос
	 * @throws DataBaseException если что-то не получилось
	 */
  public function prepare( $query ) {
    $statement = $this->connection->prepare( $query );
    if( !$statement ) {
      throw new DataBaseException( "DBConnection.prepare()", $this->connection->error, $this->connection->errno );
    }

    return new DBStatement( $statement );
  }

  /**
	 * Сохраняет транзакцию
	 * @throws DataBaseException если какая-либо ошибка в работе коммита обнаружена (rollback делается автоматически)
	 */
  public function commit() {
    if( !$this->connection->commit() ) {
      $exception = new DataBaseException( "DBConnection.commit()", $this->connection->error, $this->connection->errno );
      $this->connection->rollback();
      throw $exception;
    }
  }

  /**
	 * Откатывает транзакцию. Возвращает статус отката.
	 * @return bool статус отката
	 */
  public function rollback() {
    return $this->connection->rollback();
  }

  /**
	 * Закрытие соединение
	 */
  public function close() {
    $this->connection->close();
    self::$instance = null;
  }
}

?>
