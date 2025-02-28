<?php
use PHPUnit\Framework\TestCase;
require_once 'UserManager.php';

class UserManagerTest extends TestCase {
    private $userManager;

    protected function setUp(): void {
        $this->userManager = new UserManager();
        $this->resetDatabase(); // Réinitialisation totale de la base de données
    }

    // ✅ Réinitialiser complètement la table `users`
    private function resetDatabase() {
        $this->userManager->getPdo()->exec("TRUNCATE TABLE users");
    }

    // ✅ Test de l'ajout d'un utilisateur
    public function testAddUser() {
        $uniqueEmail = "john" . uniqid() . "@example.com";
        $result = $this->userManager->addUser("John Doe", $uniqueEmail);
        $this->assertTrue($result);

        // Vérifier que l'utilisateur a bien été ajouté
        $users = $this->userManager->getUsers();
        $this->assertCount(1, $users);
    }

    // ✅ Test de la mise à jour d'un utilisateur avec un email différent
    public function testUpdateUser() {
        $uniqueEmail = "john" . uniqid() . "@example.com";
        $this->userManager->addUser("John Doe", $uniqueEmail);
        $users = $this->userManager->getUsers();
        $userId = $users[0]['id']; // Récupération de l'ID du premier utilisateur

        $newEmail = "johnsmith" . uniqid() . "@example.com";
        $result = $this->userManager->updateUser($userId, "John Smith", $newEmail);
        $this->assertTrue($result);
    }

    // ✅ Test de la suppression d'un utilisateur
    public function testRemoveUser() {
        $uniqueEmail = "john" . uniqid() . "@example.com";
        $this->userManager->addUser("John Doe", $uniqueEmail);
        $users = $this->userManager->getUsers();
        $userId = $users[0]['id'];

        $result = $this->userManager->removeUser($userId);
        $this->assertTrue($result);

        // Vérifier que l'utilisateur n'existe plus
        $remainingUsers = $this->userManager->getUsers();
        $this->assertCount(0, $remainingUsers);
    }

    // ✅ Test de la récupération de tous les utilisateurs
    public function testGetUsers() {
        $this->resetDatabase(); // Nettoyer la base avant le test

        $this->userManager->addUser("Alice", "alice" . uniqid() . "@example.com");
        $this->userManager->addUser("Bob", "bob" . uniqid() . "@example.com");

        $users = $this->userManager->getUsers();
        $this->assertCount(2, $users);
    }

    // ❌ Test de l'ajout d'un utilisateur avec un email invalide
    public function testAddUserEmailException() {
        $this->expectException(Exception::class);
        $this->userManager->addUser("Alice", "alice@example.com");
        $this->userManager->addUser("Bob", "alice@example.com"); // Devrait lever une exception
    }

    // ❌ Test de la modification d'un utilisateur inexistant
    public function testInvalidUpdateThrowsException() {
        $this->expectException(Exception::class);
        $this->userManager->updateUser("invalid_id", "Alice", "alice@example.com"); // Doit lever une exception
    }
    

    // ❌ Test de la suppression d'un utilisateur inexistant
    public function testInvalidDeleteThrowsException() {
        $this->expectException(Exception::class);
        $this->userManager->removeUser(99);
    }
}
?>
