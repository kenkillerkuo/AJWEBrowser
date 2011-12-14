<?
require_once("passwd.php");

class DBers {
  private $db = 0;

  function __construct() {
    $this->check_conn();
  }
  private function &connectToDb($host, $dbUser, $dbPass, $dbName){
    if(!$dbConn = @mysql_connect($host, $dbUser, $dbPass)){
      return false;
    }
    if(!@mysql_select_db($dbName)){
      return flase;
    }
    return $dbConn;
  }

  private function conn(){
    if(!$this->db = &$this->connectToDb(HOST, DBUSER, DBPWD, DBNAME) ){
      die ("sql connection failed");
    }
    mysql_query("SET NAMES 'utf-8'");
  }

  private function check_conn() {
    if (empty($this->db)) {
      $this->conn();
    }
  }

  protected function query($sql) {
    $this->check_conn();
    return mysql_query($sql, $this->db);
  }

  protected function select($sql) {
    $res = $this->query($sql);

    $ret_arr = array();
    $i = 0;
    while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
      $ret_arr[$i] = $row;
      $i++;
    }

    if (is_array($ret_arr)) {
      return $ret_arr;
    } else {
      return false;
    }
  }
}

class HisDB extends DBers {
  private $table = 'WRE_history';

  function __construct() {
   // parent::construct();
  }

  function his_ins($set_arr)
  {
    $sql = "INSERT INTO WRE_history SET ";

    foreach($set_arr as $key => $val)
      $sql .= $key."='".$val."', ";

    $sql = substr($sql, 0, -2);
    return $res = $this->query($sql);

  }

  function his_sel($where_arr ='', $limit ='', $like_arr ='', $thumb = true)
  {
    if ($thumb) {
      $sql = "SELECT account,time,count,is_del FROM WRE_history ";
    } else {
      $sql = "SELECT thumb FROM WRE_history ";
    }

    if (is_array($where_arr) || is_array($like_arr)) {
      $sql .= "WHERE ";
      foreach ($where_arr as $key => $val) {
        $sql .= "$key='$val' AND ";
      }

      if (is_array($like_arr)) {
        foreach ($like_arr as $key => $val) {
          $sql .= "$key LIKE '$val%' AND ";
        }
      }
      $sql = substr($sql, 0, -4);
    }
    if($limit != '')
     $sql .= 'limit 0,'.$limit.' ';

    return $this->select($sql);
  }

  function his_list_sel()
  {
     $sql = "SELECT * FROM WRE_history ORDER BY time DESC limit 0,10";
     return $this->select($sql);
  }

  function his_up($set_arr, $account)
  {

    if(!is_array($set_arr))
      return false;

    $sql = "UPDATE WRE_history SET ";

    foreach($set_arr as $key => $val)
      $sql .= "$key = '$val', ";
    $sql = substr($sql, 0, -2)." WHERE account = '$account'";


    return $this->query($sql)? true: false;
  }
}

?>
