<?php
include_once 'Conexion.php';

class LoginAsistente {
    // Definición de visibilidad explícita y tipado (PHP 7.4+)
    private ?PDO $acceso;

    public function __construct() {
        $db = new Conexion();
        // Asegurar que manejamos el objeto pdo de la conexión
        $this->acceso = $db->pdo;
    }
    
    /**
     * Valida las credenciales del asistente.
     * @param string $cedula
     * @param string $pass
     * @return object|false Retorna el objeto del usuario si tiene éxito, o false si falla.
     */
    public function loguearse(string $cedula, string $pass) {
        try {
            $sql = "SELECT la.*, ra.nombre_asistente, ra.apellido_asistente, ra.asistente_tipo, tp.nombre_tipo
                    FROM login_asistente la
                    INNER JOIN registro_asistente ra ON la.id_asistente = ra.id_asistente
                    INNER JOIN tipo_paciente tp ON ra.asistente_tipo = tp.id_tipo_us
                    WHERE ra.cedula_asistente = :cedula AND la.status = 'activo'";
                    
            $query = $this->acceso->prepare($sql);
            $query->execute([':cedula' => $cedula]);
            $usuario = $query->fetch(PDO::FETCH_OBJ);
            
            if ($usuario && password_verify($pass, $usuario->password_hash)) {
                // Actualizamos el acceso directamente al loguearse con éxito
                $this->actualizarUltimoAcceso($usuario->id_asistente);
                
                // Quitamos el hash del objeto por seguridad antes de retornarlo
                unset($usuario->password_hash);
                return $usuario;
            }
            
            return false;
        } catch (PDOException $e) {
            // Aquí puedes registrar el error en un log interno de la app
            return false;
        }
    }
    
    /**
     * Cambia la contraseña del usuario tras validar la anterior.
     * @param int $id_asistente
     * @param string $old_pass
     * @param string $newpass
     * @return bool True si se actualizó con éxito, false en caso contrario.
     */
    public function cambiarContra(int $id_asistente, string $old_pass, string $newpass): bool {
        try {
            $sql = "SELECT password_hash FROM login_asistente WHERE id_asistente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id' => $id_asistente]);
            $usuario = $query->fetch(PDO::FETCH_OBJ);
            
            if ($usuario && password_verify($old_pass, $usuario->password_hash)) {
                $new_hash = password_hash($newpass, PASSWORD_DEFAULT);
                
                $sql = "UPDATE login_asistente SET password_hash = :newpass WHERE id_asistente = :id";
                $query = $this->acceso->prepare($sql);
                $resultado = $query->execute([':id' => $id_asistente, ':newpass' => $new_hash]);
                
                return $resultado; // Retorna true si la consulta fue exitosa
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Actualiza la marca de tiempo del último ingreso.
     * @param int $id_asistente
     */
    private function actualizarUltimoAcceso(int $id_asistente): void {
        try {
            $sql = "UPDATE login_asistente SET ultimo_acceso = NOW() WHERE id_asistente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id' => $id_asistente]);
        } catch (PDOException $e) {
            // Falla silenciosa para no interrumpir el flujo principal del login
        }
    }
}
?>
