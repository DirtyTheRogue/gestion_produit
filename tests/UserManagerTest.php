<?php
use PHPUnit\Framework\TestCase;
require_once 'UserManager.php';

class UserManagerTest extends TestCase {
    private $userManager;

    protected function setUp(): void {
        $this->userManager = new UserManager();
        $this->resetDatabase(); 
    }

    private function resetDatabase() {
        $this->userManager->getPdo()->exec("TRUNCATE TABLE users");
    }

    public function testAddUser() {
        $uniqueEmail = "john" . uniqid() . "@example.com";
        $result = $this->userManager->addUser("John Doe", $uniqueEmail);
        $this->assertTrue($result);

        $users = $this->userManager->getUsers();
        $this->assertCount(1, $users);
    }

    public function testUpdateUser() {
        $uniqueEmail = "john" . uniqid() . "@example.com";
        $this->userManager->addUser("John Doe", $uniqueEmail);
        $users = $this->userManager->getUsers();
        $userId = $users[0]['id']; 

        $newEmail = "johnsmith" . uniqid() . "@example.com";
        $result = $this->userManager->updateUser($userId, "John Smith", $newEmail);
        $this->assertTrue($result);
    }

    public function testRemoveUser() {
        $uniqueEmail = "john" . uniqid() . "@example.com";
        $this->userManager->addUser("John Doe", $uniqueEmail);
        $users = $this->userManager->getUsers();
        $userId = $users[0]['id'];

        $result = $this->userManager->removeUser($userId);
        $this->assertTrue($result);

        $remainingUsers = $this->userManager->getUsers();
        $this->assertCount(0, $remainingUsers);
    }

    public function testGetUsers() {
        $this->resetDatabase(); 

        $this->userManager->addUser("Alice", "alice" . uniqid() . "@example.com");
        $this->userManager->addUser("Bob", "bob" . uniqid() . "@example.com");

        $users = $this->userManager->getUsers();
        $this->assertCount(2, $users);
    }

    public function testAddUserEmailException() {
        $this->expectException(Exception::class);
        $this->userManager->addUser("Alice", "alice@example.com");
        $this->userManager->addUser("Bob", "alice@example.com"); 
    }

    public function testInvalidUpdateThrowsException() {
        $this->expectException(Exception::class);
        $this->userManager->updateUser(99, "Fake User", "fake@example.com");
    }

    public function testInvalidDeleteThrowsException() {
        $this->expectException(Exception::class);
        $this->userManager->removeUser(99);
    }
}
?>
