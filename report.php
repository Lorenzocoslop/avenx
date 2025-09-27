<?php

// Configurações básicas
header('Content-Type: application/json');

// Configurações de segurança
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    exit;
}

// Receber e decodificar o JSON enviado
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Validações básicas do payload
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Requisição inválida
    echo json_encode(["error" => "Formato de JSON inválido."]);
    exit;
}

$requiredFields = ['report_id', 'date', 'client', 'summary', 'details', 'actions', 'recommendations'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        http_response_code(400); // Requisição inválida
        echo json_encode(["error" => "Campo obrigatório ausente: $field"]);
        exit;
    }
}

// Sanitizar e validar campos importantes (exemplo para 'date')
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
    http_response_code(400);
    echo json_encode(["error" => "Formato de data inválido. Use AAAA-MM-DD."]);
    exit;
}

// Salvar o relatório no banco de dados ou em um arquivo
$storagePath = __DIR__ . '/reports/';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
}

$reportFile = $storagePath . 'report_' . htmlspecialchars($data['report_id']) . '.json';
if (file_put_contents($reportFile, json_encode($data, JSON_PRETTY_PRINT))) {
    http_response_code(200); // Sucesso
    echo json_encode(["success" => "Relatório recebido e armazenado com sucesso."]);
} else {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(["error" => "Erro ao salvar o relatório."]);
}