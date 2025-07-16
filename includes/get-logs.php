<?php
require_once 'auth.php';
require_once 'config.php';
Auth::requireAdmin();

header('Content-Type: application/json');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Get DataTables parameters
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 25;
$search = $_GET['search']['value'] ?? '';
$orderColumn = $_GET['order'][0]['column'] ?? 0;
$orderDir = $_GET['order'][0]['dir'] ?? 'desc';

// Column mapping
$columns = [
    'sys_logs.id',
    'sys_logs.name',
    'sys_logs.description',
    'creator',
    'sys_logs.datetime_added'
];

// Base query
$query = "SELECT sys_logs.*, users.first_name, users.last_name 
          FROM sys_logs 
          LEFT JOIN users ON sys_logs.creator = users.id";

// Add search condition if needed
if (!empty($search)) {
    $query .= " WHERE sys_logs.name LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR sys_logs.description LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR CONCAT(users.first_name, ' ', users.last_name) LIKE '%" . $conn->real_escape_string($search) . "%'";
}

// Add ordering
$query .= " ORDER BY " . $columns[$orderColumn] . " " . ($orderDir === 'asc' ? 'ASC' : 'DESC');

// Add pagination
$query .= " LIMIT " . intval($start) . ", " . intval($length);

// Execute query
$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $row['creator_name'] = $row['first_name'] && $row['last_name'] 
        ? $row['first_name'] . ' ' . $row['last_name'] 
        : null;
    $data[] = $row;
}

// Get total records
$totalQuery = "SELECT COUNT(*) as total FROM sys_logs";
$totalResult = $conn->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];

// Get filtered total (if searching)
$filteredTotal = $totalRecords;
if (!empty($search)) {
    $filteredQuery = str_replace("SELECT sys_logs.*, users.first_name, users.last_name", 
                                "SELECT COUNT(*) as filtered", $query);
    $filteredQuery = preg_replace('/LIMIT \d+, \d+/', '', $filteredQuery);
    $filteredResult = $conn->query($filteredQuery);
    $filteredTotal = $filteredResult->fetch_assoc()['filtered'];
}

echo json_encode([
    'draw' => intval($_GET['draw'] ?? 1),
    'recordsTotal' => intval($totalRecords),
    'recordsFiltered' => intval($filteredTotal),
    'data' => $data
]);

$conn->close();
?>