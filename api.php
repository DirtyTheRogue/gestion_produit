<?php
require_once 'UserManager.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$userManager = new UserManager();

$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST') {
    if (isset($_POST['name'], $_POST['email'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
    } elseif (!empty($data) && isset($data['name'], $data['email'])) {
        $name = $data['name'];
        $email = $data['email'];
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Requête invalide. Données manquantes."]);
        exit;
    }

    $userManager->addUser($name, $email);
    echo json_encode(["message" => "Utilisateur ajouté avec succès"]);
} 

elseif ($method === 'GET') {
    echo json_encode($userManager->getUsers());
} 

elseif ($method === 'DELETE' && isset($_GET['id'])) {
    file_put_contents('log_delete.txt', "ID reçu pour suppression : " . $_GET['id'] . PHP_EOL, FILE_APPEND);
    $userManager->removeUser($_GET['id']);
    echo json_encode(["message" => "Utilisateur supprimé"]);

} 

elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    if (isset($_PUT['id'], $_PUT['name'], $_PUT['email'])) {
        $userManager->updateUser($_PUT['id'], $_PUT['name'], $_PUT['email']);
        echo json_encode(["message" => "Utilisateur mis à jour"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Requête invalide. Données PUT manquantes."]);
    }
} 

else {
    http_response_code(400);
    echo json_encode(["error" => "Requête invalide."]);
}
?>
