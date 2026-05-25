<?php
include_once 'Conexion.php';

class DocumentoMedico {
    private $acceso;

    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    public function listarPorRol($rol, $id_usuario) {
        $where = 'r.estado = 1';
        $params = [];

        if ($rol === 'paciente') {
            $where .= ' AND r.id_paciente = :id_usuario';
            $params[':id_usuario'] = $id_usuario;
        }

        $sql = "SELECT r.*, 
                       CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) AS nombre_paciente,
                       rp.cedula_paciente,
                       rp.sexo_paciente,
                       rp.fecha_nacimiento_pac,
                       CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) AS nombre_medico,
                       d.diagnostico,
                       d.trat_sugerido,
                       e.est_solicitado,
                       e.obs_adicional
                FROM recetas r
                LEFT JOIN registro_paciente rp ON r.id_paciente = rp.id_paciente
                LEFT JOIN registro_medico rm ON r.id_medico = rm.id_medico
                LEFT JOIN diagnostico_rec d ON r.id_receta = d.id_receta
                LEFT JOIN est_laboratorio e ON r.id_receta = e.id_receta
                WHERE {$where}
                ORDER BY r.fecha_receta DESC, r.id_receta DESC";

        $query = $this->acceso->prepare($sql);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerPorId($id_receta, $rol, $id_usuario) {
        $where = 'r.estado = 1 AND r.id_receta = :id_receta';
        $params = [':id_receta' => $id_receta];

        if ($rol === 'paciente') {
            $where .= ' AND r.id_paciente = :id_usuario';
            $params[':id_usuario'] = $id_usuario;
        }

        $sql = "SELECT r.*, 
                       CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) AS nombre_paciente,
                       rp.cedula_paciente,
                       rp.sexo_paciente,
                       rp.fecha_nacimiento_pac,
                       CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) AS nombre_medico,
                       d.diagnostico,
                       d.trat_sugerido,
                       e.est_solicitado,
                       e.obs_adicional
                FROM recetas r
                LEFT JOIN registro_paciente rp ON r.id_paciente = rp.id_paciente
                LEFT JOIN registro_medico rm ON r.id_medico = rm.id_medico
                LEFT JOIN diagnostico_rec d ON r.id_receta = d.id_receta
                LEFT JOIN est_laboratorio e ON r.id_receta = e.id_receta
                WHERE {$where}
                LIMIT 1";

        $query = $this->acceso->prepare($sql);
        $query->execute($params);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}
?>
