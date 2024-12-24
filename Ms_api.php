<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ڈیٹا اسٹور کرنے والی فائل
$dataFile = "user_data.json";

// اگر فائل موجود نہیں ہے تو ایک خالی JSON بنائیں
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

// کلائنٹ کا یوزر آئی ڈی (مثلاً: یوزر کے آئی پی ایڈریس یا کوئی یونیک ID)
function getUserID() {
    return $_SERVER['REMOTE_ADDR']; // IP ایڈریس کو یوزر آئی ڈی کے طور پر استعمال کریں
}

// ڈیٹا لوڈ کریں
function loadUserData($userID) {
    global $dataFile;
    $allData = json_decode(file_get_contents($dataFile), true);
    return isset($allData[$userID]) ? $allData[$userID] : [
        "totalAdsWatched" => 0,
        "totalVideosWatched" => 0,
        "totalEarnings" => 0.0,
        "history" => []
    ];
}

// ڈیٹا محفوظ کریں
function saveUserData($userID, $userData) {
    global $dataFile;
    $allData = json_decode(file_get_contents($dataFile), true);
    $allData[$userID] = $userData;
    file_put_contents($dataFile, json_encode($allData, JSON_PRETTY_PRINT));
}

// ایکشنز ہینڈل کریں
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $userID = getUserID();

    // لوڈ ڈیٹا ایکشن
    if ($action === 'loadUserData') {
        $userData = loadUserData($userID);
        echo json_encode($userData);
        exit;

    // سیو ڈیٹا ایکشن
    } elseif ($action === 'saveUserData') {
        $input = json_decode(file_get_contents("php://input"), true);

        if (is_array($input)) {
            saveUserData($userID, $input);
            echo json_encode(["success" => true, "message" => "Data saved successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid data format"]);
        }
        exit;

    // غیر متعلقہ ایکشنز
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit;
    }
}

// اگر ایکشن فراہم نہ کیا جائے
echo json_encode(["success" => false, "message" => "No action specified"]);
exit;
?>