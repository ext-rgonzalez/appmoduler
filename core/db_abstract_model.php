<?php
abstract class DBAbstractModel {

    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_pass = '';
    protected $db_name      = '';
    protected $query;
    protected $rows         = array();
    protected $row;
    protected $rowAffected;
    private $conn;
    public $msj             = '';
    public $err             = '';
    public $nombre;
    public $apellido;
    public $email;
    public $usuario;
    public $password;
    public $cod_estado;
    public $img;
    public $_data = array();
    public $_session = array();
    public $_sessionVal = array();
    public $_empresa = array();
    public $_mensajes = array();
    public $_numMensajes = array();
    public $_menu = array();
    public $_menuHeader = array();
    public $_menuShorcut = array();
    public $_notificacion = array();
    public $_tarea = array();
    public $_numNotificacion = array();
    public $_navegacion = array();
    public $_formulario = array();
    public $_formulario_ayuda = array();
    public $_formulario_modal = array();
    public $_boton = array();
    public $_frame = array();
    public $_input = array();
    public $_tabla = array();
    public $_cTabla = array();
    public $_secuencia;
    # métodos abstractos para ABM de clases que hereden    
    /*abstract protected function get();
    abstract protected function set();
    abstract protected function edit();
    abstract protected function delete();*/
    
    # los siguientes métodos pueden definirse con exactitud y no son abstractos
	# Conectar a la base de datos
	private function open_connection() {
	    $this->conn = new mysqli(self::$db_host, self::$db_user, 
	                             self::$db_pass, $this->db_name);
            $this->conn->set_charset("utf8");
	}

	# Desconectar la base de datos
	private function close_connection() {
		$this->conn->close();
	}

	# Ejecutar un query simple del tipo INSERT, DELETE, UPDATE
	protected function execute_single_query() {
	    if($_POST || $_GET) {
	        $this->open_connection();
	        $this->conn->query($this->query);
                $this->rowAffected=mysqli_affected_rows($this->conn);
	        $this->close_connection();
	    } else {
	        $this->mensaje = 'Metodo no permitido';
	    }
	}

	# Traer resultados de una consulta en un Array
	protected function get_results_from_query() {
            $this->open_connection();
            $result = $this->conn->query($this->query);
            while ($this->rows[] = $result->fetch_assoc());
            //$result->close();
            //self::close_connection();
            array_pop($this->rows);
	}
	
        protected function get_results_from_sp() {
            $this->open_connection();
            array_pop($this->rows);
            $this->conn->query($this->query);
            $result = $this->conn->query( "SELECT @cDesError,@cNumError" );
            $this->row = $result->fetch_assoc();

	}
        
	# Traer 1 resultado de una consulta
	protected function get_result_from_query() {
            $this->open_connection();
            $result = $this->conn->query($this->query);
            $this->row = $result->fetch_assoc();
            //$result->close();
            $this->close_connection();
	}
}
?>
