<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'PLOJA');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar usuário admin
$result = $conn->query("SELECT * FROM users WHERE email = 'admin@admin.com'");
$user = $result->fetch_assoc();

echo json_encode([
    'user_exists' => $user !== null,
    'user_data' => $user ? [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name']
    ] : null
]);
