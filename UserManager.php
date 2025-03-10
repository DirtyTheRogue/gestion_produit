<?php
class UserManager {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=user_management", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    public function getPdo() {
        return $this->pdo;
    }

    public function addUser($name, $email)
{
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        throw new Exception("Cet email est déjà utilisé par un autre utilisateur.");
    }

    $stmt = $this->pdo->prepare("INSERT INTO users (name, email, date_added) VALUES (:name, :email, NOW())");
    return $stmt->execute(['name' => $name, 'email' => $email]);

}



    public function updateUser($id, $name, $email) {
    if (!is_numeric($id) || empty($name) || empty($email)) {
        throw new Exception("ID, nom ou email invalide.");
    }

    $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $stmt->execute([':email' => $email, ':id' => $id]);

    if ($stmt->fetch()) {
        throw new Exception("L'email $email est déjà utilisé par un autre utilisateur.");
    }

    $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
    $stmt->execute([':name' => $name, ':email' => $email, ':id' => $id]);

    error_log("Mise à jour de l'utilisateur ID : " . $id);
    return true;
}


    
    
    public function removeUser($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID invalide.");
        }
    
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
    
        if (!$user) {
            throw new Exception("Utilisateur non trouvé.");
        }
    
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
    
        error_log("Suppression de l'utilisateur avec ID : " . $id);
        return true;
    }
   
    
    


    public function getUsers() {
        $stmt = $this->pdo->query("SELECT id, name, email, date_added FROM users ORDER BY date_added DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
