<?php
header('Content-Type: application/json');
require_once "function.php";
/** @var mysqli $conn */

$action = $_POST['action'] ?? "";
$search = $_POST['search'] ?? "";
$data   = ["results" => []];

try {
    switch ($action) {
        case "searchBucket":
            $sql = "SELECT DISTINCT bucket AS id, bucket AS text FROM tbl_master_barcode WHERE bucket LIKE ?";
            $params = ["%$search%"];
            $types  = "s";

            if (!empty($_POST['style'])) {
                $sql .= " AND style = ?";
                $params[] = $_POST['style'];
                $types .= "s";
            }
            if (!empty($_POST['item'])) {
                $sql .= " AND item = ?";
                $params[] = $_POST['item'];
                $types .= "s";
            }
            if (!empty($_POST['po'])) {
                $sql .= " AND po = ?";
                $params[] = $_POST['po'];
                $types .= "s";
            }

            if (!empty($_POST['po_item'])) {
                $sql .= " AND po_item = ?";
                $params[] = $_POST['po_item'];
                $types .= "s";
            }

            if (!empty($_POST['line'])) {
                $sql .= " AND line = ?";
                $params[] = $_POST['line'];
                $types .= "s";
            }

            $sql .= " ORDER BY bucket ASC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            break;

        case "searchStyle":
            $sql = "SELECT DISTINCT style AS id, style AS text FROM tbl_master_barcode WHERE style LIKE ?";
            $params = ["%$search%"];
            $types  = "s";

            if (!empty($_POST['bucket'])) {
                $sql .= " AND bucket = ?";
                $params[] = $_POST['bucket'];
                $types .= "s";
            }
            if (!empty($_POST['item'])) {
                $sql .= " AND item = ?";
                $params[] = $_POST['item'];
                $types .= "s";
            }
            if (!empty($_POST['po'])) {
                $sql .= " AND po = ?";
                $params[] = $_POST['po'];
                $types .= "s";
            }

            if (!empty($_POST['po_item'])) {
                $sql .= " AND po_item = ?";
                $params[] = $_POST['po_item'];
                $types .= "s";
            }

            if (!empty($_POST['line'])) {
                $sql .= " AND line = ?";
                $params[] = $_POST['line'];
                $types .= "s";
            }

            $sql .= " ORDER BY style ASC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            break;

        case "searchItem":
            $sql = "SELECT DISTINCT item AS id, item AS text FROM tbl_master_barcode WHERE item LIKE ?";
            $params = ["%$search%"];
            $types  = "s";

            if (!empty($_POST['bucket'])) {
                $sql .= " AND bucket = ?";
                $params[] = $_POST['bucket'];
                $types .= "s";
            }
            if (!empty($_POST['style'])) {
                $sql .= " AND style = ?";
                $params[] = $_POST['style'];
                $types .= "s";
            }
            if (!empty($_POST['po'])) {
                $sql .= " AND po = ?";
                $params[] = $_POST['po'];
                $types .= "s";
            }

            if (!empty($_POST['po_item'])) {
                $sql .= " AND po_item = ?";
                $params[] = $_POST['po_item'];
                $types .= "s";
            }

            if (!empty($_POST['line'])) {
                $sql .= " AND line = ?";
                $params[] = $_POST['line'];
                $types .= "s";
            }

            $sql .= " ORDER BY item ASC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            break;

        case "searchPo":
            $sql = "SELECT DISTINCT po AS id, po AS text FROM tbl_master_barcode WHERE po LIKE ?";
            $params = ["%$search%"];
            $types  = "s";

            if (!empty($_POST['bucket'])) {
                $sql .= " AND bucket = ?";
                $params[] = $_POST['bucket'];
                $types .= "s";
            }
            if (!empty($_POST['style'])) {
                $sql .= " AND style = ?";
                $params[] = $_POST['style'];
                $types .= "s";
            }
            if (!empty($_POST['item'])) {
                $sql .= " AND item = ?";
                $params[] = $_POST['item'];
                $types .= "s";
            }

            if (!empty($_POST['po_item'])) {
                $sql .= " AND po_item = ?";
                $params[] = $_POST['po_item'];
                $types .= "s";
            }

            if (!empty($_POST['line'])) {
                $sql .= " AND line = ?";
                $params[] = $_POST['line'];
                $types .= "s";
            }

            $sql .= " ORDER BY po ASC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            break;

        case "searchPoItem":
            $sql = "SELECT DISTINCT po_item AS id, po_item AS text FROM tbl_master_barcode WHERE po_item LIKE ?";
            $params = ["%$search%"];
            $types  = "s";

            if (!empty($_POST['bucket'])) {
                $sql .= " AND bucket = ?";
                $params[] = $_POST['bucket'];
                $types .= "s";
            }
            if (!empty($_POST['style'])) {
                $sql .= " AND style = ?";
                $params[] = $_POST['style'];
                $types .= "s";
            }
            if (!empty($_POST['item'])) {
                $sql .= " AND item = ?";
                $params[] = $_POST['item'];
                $types .= "s";
            }

            if (!empty($_POST['po'])) {
                $sql .= " AND po = ?";
                $params[] = $_POST['po'];
                $types .= "s";
            }

            if (!empty($_POST['line'])) {
                $sql .= " AND line = ?";
                $params[] = $_POST['line'];
                $types .= "s";
            }

            $sql .= " ORDER BY po_item ASC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            break;

        case "searchLine":
            $sql = "SELECT DISTINCT line AS id, line AS text FROM tbl_master_barcode WHERE line LIKE ?";
            $params = ["%$search%"];
            $types  = "s";

            if (!empty($_POST['bucket'])) {
                $sql .= " AND bucket = ?";
                $params[] = $_POST['bucket'];
                $types .= "s";
            }
            if (!empty($_POST['style'])) {
                $sql .= " AND style = ?";
                $params[] = $_POST['style'];
                $types .= "s";
            }
            if (!empty($_POST['item'])) {
                $sql .= " AND item = ?";
                $params[] = $_POST['item'];
                $types .= "s";
            }

            if (!empty($_POST['po'])) {
                $sql .= " AND po = ?";
                $params[] = $_POST['po'];
                $types .= "s";
            }

            if (!empty($_POST['po_item'])) {
                $sql .= " AND po_item = ?";
                $params[] = $_POST['po_item'];
                $types .= "s";
            }

            $sql .= " ORDER BY line ASC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            break;

        default:
            echo json_encode($data);
            exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data['results'][] = $row;
    }
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $data['error'] = $e->getMessage();
}

echo json_encode($data);
