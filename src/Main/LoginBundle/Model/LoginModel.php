<?php

//http://www.mustbebuilt.co.uk/php/insert-update-and-delete-with-pdo/
//http://code.tutsplus.com/tutorials/why-you-should-be-using-phps-pdo-for-database-access--net-12059

namespace Main\LoginBundle\Model;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

set_time_limit(300);

class LoginModel {

    private $host;
    private $port;
    private $name;
    private $user;
    private $pass;
    public $dbh;

    function __construct() {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . "/../../../../app/config"));
        $loader->load('parameters.yml');

        $this->host = $container->getParameter("database_host");
        $this->name = $container->getParameter("database_name");
        $this->user = $container->getParameter("database_user");
        $this->pass = $container->getParameter("database_password");
        $this->port = $container->getParameter("database_port");

        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $this->dbh = new \PDO("mysql:host=$this->host;dbname=$this->name;port=$this->port", $this->user, $this->pass, $options);
    }

    public function getEstados($args) {
        $qry = 'SELECT id, nombre, abbr FROM tbl_estados WHERE id_pais=:id_pais ORDER BY nombre';
        $sth = $this->dbh->prepare($qry);
        $sth->bindParam(':id_pais', $args["id_pais"], \PDO::PARAM_INT);
        $sth->execute();
        $status = $sth->execute();
        $data = $sth->fetchAll();
        $r = array('status' => $status, 'data' => $data);
        $result = $this->orderByKey($r, "id", "onlydata");
        return $result;
    }

//
//    public function insertEvent($args) {
//        $qry = 'INSERT INTO evento 
//            VALUES("", :Nombre, CURRENT_TIMESTAMP())';
//        $sth = $this->dbh->prepare($qry);
//        $sth->bindParam(':Nombre', $args["Nombre"], \PDO::PARAM_STR, 120);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEvento"] = ltrim($this->dbh->lastInsertId(), '0'); //remover ceros a la izquierda
//        $result["data"]["code"] = $code;
//        $result["data"]["Nombre"] = $args["Nombre"];
//        return $result;
//    }
//
//    public function updateEvent($idEvento, $args) {
//
//        $sth = $this->dbh->prepare('UPDATE evento SET Nombre= :Nombre WHERE idEvento = :idEvento');
//        $sth->bindParam(":idEvento", $idEvento, \PDO::PARAM_INT);
//        $sth->bindParam(':Nombre', $args["Nombre"], \PDO::PARAM_STR);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEvento"] = ltrim($idEvento, '0'); //remover ceros a la izquierda
//        $result["data"]["code"] = $code;
//        $result["data"]["Nombre"] = $args["Nombre"];
//        return $result;
//    }
//
//    public function deleteEvent($idEvento) {
//
//        $sth = $this->dbh->prepare('DELETE FROM evento WHERE idEvento = :idEvento');
//        $sth->bindParam(":idEvento", $idEvento, \PDO::PARAM_INT);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEvento"] = ltrim($idEvento, '0'); //remover ceros a la izquierda
//        $result["data"]["code"] = $code;
//        return $result;
//    }
//
//    public function insertEdition($args) {
//
//        $qry = 'INSERT INTO edicion 
//            VALUES("", :actualizar, :fechaActualizacion,:Nombre, CURRENT_TIMESTAMP(), :idEvento, "", :RawDB, :estructura, :odbc_wsdl, :restrictVisitantes, :fmEdicion, :fecha_ini, :fecha_fin)';
//
//        $sth = $this->dbh->prepare($qry);
//        $sth->bindParam(':actualizar', $args["actualizar"], \PDO::PARAM_INT);
//        $sth->bindParam(':fechaActualizacion', $args["fechaActualizacion"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':Nombre', $args["Nombre"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':idEvento', $args["idEvento"], \PDO::PARAM_INT);
//        $sth->bindParam(':RawDB', $args["RawDB"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':estructura', $args["estructura"], \PDO::PARAM_INT);
//        $sth->bindParam(':odbc_wsdl', $args["odbc_wsdl"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':restrictVisitantes', $args["restrictVisitantes"], \PDO::PARAM_INT);
//        $sth->bindParam(':fmEdicion', $args["fmEdicion"], \PDO::PARAM_INT);
//        $sth->bindParam(':fecha_ini', $args["fecha_ini"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':fecha_fin', $args["fecha_fin"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':idEventoSL', $args["idEventoSL"], \PDO::PARAM_INT);
//        $sth->bindParam(':idEdicionSL', $args["idEdicionSL"], \PDO::PARAM_INT);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEdicion"] = ltrim($this->dbh->lastInsertId(), '0');
//        $result["data"]["code"] = $code;
//        return $result;
//    }
//
//    public function checkEdiciones($args) {
//        $sth = $this->dbh->prepare("
//                SELECT idEdicion FROM edicion
//                WHERE Nombre= :Nombre AND idEvento= :idEvento");
//        $sth->bindParam(':Nombre', $args["Nombre"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':idEvento', $args["idEvento"], \PDO::PARAM_STR, 120);
//        $sth->execute();
//
//        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
//
//        return $result;
//    }
//
//    public function insertModuleEdition($edicion) {
//        $sth = $this->dbh->prepare('INSERT INTO moduloedicion 
//            VALUES("", 1, :idEdicion , CURRENT_TIMESTAMP() )');
//        $sth->bindParam(':idEdicion', $edicion, \PDO::PARAM_INT);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEdicion"] = ltrim($this->dbh->lastInsertId(), '0');
//        $result["data"]["code"] = $code;
//        return $result;
//    }
//
//    public function updateEdition($idEdicion, $args) {
//
//        $sth = $this->dbh->prepare('UPDATE edicion SET Nombre= :Nombre, actualizar= :actualizar, fechaActualizacion= :fechaActualizacion,Nombre= :Nombre, idEvento= :idEvento, RawDB= :RawDB, estructura= :estructura, odbc_wsdl=:odbc_wsdl, restrictVisitantes= :restrictVisitantes, fmEdicion=:fmEdicion, fecha_ini=:fecha_ini, fecha_fin=:fecha_fin WHERE idEdicion = :idEdicion');
//        $sth->bindParam(":idEdicion", $idEdicion, \PDO::PARAM_INT);
//        $sth->bindParam(':actualizar', $args["actualizar"], \PDO::PARAM_INT);
//        $sth->bindParam(':fechaActualizacion', $args["fechaActualizacion"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':Nombre', $args["Nombre"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':idEvento', $args["idEvento"], \PDO::PARAM_INT);
//        $sth->bindParam(':RawDB', $args["RawDB"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':estructura', $args["estructura"], \PDO::PARAM_INT);
//        $sth->bindParam(':odbc_wsdl', $args["odbc_wsdl"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':restrictVisitantes', $args["restrictVisitantes"], \PDO::PARAM_INT);
//        $sth->bindParam(':fmEdicion', $args["fmEdicion"], \PDO::PARAM_INT);
//        $sth->bindParam(':fecha_ini', $args["fecha_ini"], \PDO::PARAM_STR, 120);
//        $sth->bindParam(':fecha_fin', $args["fecha_fin"], \PDO::PARAM_STR, 120);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEdicion"] = ltrim($idEdicion, '0'); //remover ceros a la izquierda
//        $result["data"]["code"] = $code;
//        $result["data"]["Nombre"] = $args["Nombre"];
//        return $result;
//    }
//
//    public function deleteEdition($idEdicion) {
//
//        $sth = $this->dbh->prepare('DELETE FROM edicion WHERE idEdicion = :idEdicion');
//        $sth->bindParam(":idEdicion", $idEdicion, \PDO::PARAM_INT);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEdicion"] = ltrim($idEdicion, '0'); //remover ceros a la izquierda
//        $result["data"]["code"] = $code;
//        return $result;
//    }
//
//    public function updateConnection($idEdicion, $fmConexion) {
//
//        $sth = $this->dbh->prepare('UPDATE edicion SET fmConexion= :fmConexion WHERE idEdicion = :idEdicion');
//        $sth->bindParam(":idEdicion", $idEdicion, \PDO::PARAM_INT);
//        $sth->bindParam(':fmConexion', $fmConexion, \PDO::PARAM_STR);
//        $sth->execute();
//        $code = intval($sth->errorCode());
//        $result["status"] = ($code == 0) ? 1 : 0;
//        $result["data"]["idEdicion"] = ltrim($idEdicion, '0'); //remover ceros a la izquierda
//        $result["data"]["code"] = $code;
//        return $result;
//    }
//
//    public function getEditions() {
//        $qry = 'SELECT ev.idEvento, ev.Nombre as Evento, ed.actualizar, ed.idEdicion, ';
//        $qry.= 'ed.fechaActualizacion, ed.Nombre as Edicion, ed.creacion, ed.fmConexion, ';
//        $qry.= 'ed.RawDB, ed.estructura, ed.odbc_wsdl, ed.restrictVisitantes, ed.fmEdicion, ed.fecha_ini, ed.fecha_fin, ed.idEventoSL, ed.idEdicionSL ';
//        $qry.= 'FROM edicion ed JOIN evento ev ON ev.idEvento=ed.idEvento ORDER BY Evento ASC, Edicion ASC';
//        $sth = $this->dbh->prepare($qry);
//        $sth->execute();
//        $status = $sth->execute();
//        $data = $sth->fetchAll();
//        $r = array('status' => $status, 'data' => $data);
//        $result = $this->orderByKey($r, "idEdicion", "onlydata");
//        return $result;
//    }
//
//    public function getUsers() {
//        $qry = 'SELECT 	u.idUsuario, u.idRol, u.nombre, u.password, u.apellidoPaterno, u.nombreUsuario, u.mail, u.idParent, r.Nombre as "NombreRol" ';
//        $qry.= 'FROM usuario u JOIN rol r ON u.idRol= r.idRol ';
//        $qry.= 'ORDER BY u.nombre, u.apellidoPaterno';
//        $sth = $this->dbh->prepare($qry);
//        $sth->execute();
//        $status = $sth->execute();
//        $data = $sth->fetchAll();
//        $r = array('status' => $status, 'data' => $data);
//        $result = $this->orderByKey($r, "idUsuario", "onlydata");
//        return $result;
//    }
//
//    public function getEditionsPost($idEvento) {
//        $qry = 'SELECT idEdicion,Nombre FROM edicion WHERE idEvento = :idEvento';
//        $sth = $this->dbh->prepare($qry);
//        $sth->bindParam(":idEvento", $idEvento, \PDO::PARAM_INT);
//        $status = $sth->execute();
//        $data = $sth->fetchAll();
//        $r = array('status' => $status, 'data' => $data);
//        $result = $this->orderByKey($r, "idEdicion", "");
//        return $result;
//    }
//
//    public function getModulesPost($idEdicion) {
//        $qry = 'SELECT me.idModuloEdicion, me.idModulo , m.Nombre FROM moduloedicion me
//                LEFT JOIN modulo m ON m.idModulo = me.idModulo
//                WHERE me.idEdicion = :idEdicion ';
//        $sth = $this->dbh->prepare($qry);
//        $sth->bindParam(":idEdicion", $idEdicion, \PDO::PARAM_INT);
//        $status = $sth->execute();
//        $data = $sth->fetchAll();
//        $r = array('status' => $status, 'data' => $data);
//        $result = $this->orderByKey($r, "idModuloEdicion", "");
//        return $result;
//    }
//
//    public function getObjectsPost($idModuloEdicion) {
//        $qry = 'SELECT * FROM objeto o
//                JOIN moduloobjeto mo ON o.idObjeto = mo.idObjeto
//                JOIN moduloedicion me ON mo.idModulo = me.idModulo
//                WHERE                                    
//                me.idModuloEdicion = :idModuloEdicion';
//        $sth = $this->dbh->prepare($qry);
//        $sth->bindParam(":idModuloEdicion", $idModuloEdicion, \PDO::PARAM_INT);
//        $status = $sth->execute();
//        $data = $sth->fetchAll();
//        $r = array('status' => $status, 'data' => $data);
//        $result = $this->orderByKey($r, "idObjeto", "");
//        return $result;
//    }

    public function orderByKey($result, $orderKey, $onlydata) {
        $response = '';
        if ($result['status']) {
            $result_temp = Array();
            foreach ($result['data'] as $key => $value) {
                $result_temp[$value[$orderKey]] = $value;
            }
            clearstatcache();
            $result = Array('status' => TRUE, 'data' => $result_temp);
            $response = (!empty($onlydata)) ? $result['data'] : $result; //regresar todos result o solo data
        } else if (count($result['data']) == 0) {
            /* Verifica si regresó resultados... si no regresa la el status con [data] en NULL */
            $response = $result;
        }

        return $response;
    }

}
