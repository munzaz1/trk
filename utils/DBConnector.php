<?

class DBConnector {

  function __construct($dbServer, $dbUser, $dbPassword, $dbDatabase) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $this->mysqli = new mysqli($dbServer, $dbUser, $dbPassword, $dbDatabase);
    $this->mysqli->set_charset("utf8");
  }


  public function close() {
    $this->mysqli->close();
  }

  /**
    @param string String to escape
   */
  public function escapeString($string) {
    return $this->mysqli->real_escape_string($string);
  }


  public function row($query) {
    $result = $this->mysqli->query($query);
    $row = $result->fetch_assoc();
    $result->free();
    return $row;
  }


  public function rows($query) {
    $result = $this->mysqli->query($query);
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result->free();
    return $rows;
  }


  public function rowsByColumnValue($query, $columnName) {
    $rows = $this->rows($query);
    $rowsByValue = array();
    foreach ($rows as $row) {
      $rowsByValue[$row[$columnName]] = $row;
    }
    return $rowsByValue;
  }


  public function column($query, $columnName) {
    $rows = $this->rows($query);
    $column = array();
    foreach ($rows as $row) {
      $column[] = $row[$columnName];
    }
    return $column;
  }


  public function value($query) {
    $result = $this->mysqli->query($query);
    $row = $result->fetch_row();
    $result->free();
    if ($row === NULL) {
      return NULL;
    } else {
      return $row[0];
    }
  }


  public function query($query) {
    return $this->mysqli->query($query);
  }


  public function multiQuery($query) {
    $result = $this->mysqli->multi_query($query);
  }


  public function lastInsertedId() {
    return $this->mysqli->insert_id;
  }


  // errors {{{

  public function lastErrorNo() {
    return $this->mysqli->errno;
  }


  public function lastError() {
    return $this->mysqli->error;
  }

  // }}}

}

?>
