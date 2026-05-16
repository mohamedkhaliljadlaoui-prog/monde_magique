<?php
// ===================================
// SHOP.PHP - Shop API
// ===================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Transaction.php';

$database = new Database();
$db = $database->getConnection();

$response = ['success' => false];

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';
$token = $data['token'] ?? '';

// Get user ID from token
function getUserIdFromToken($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    
    $payload = json_decode(base64_decode($parts[1]), true);
    return $payload['user_id'] ?? null;
}

$userId = getUserIdFromToken($token);

if (!$userId) {
    $response['message'] = 'Invalid token';
    echo json_encode($response);
    exit;
}

// Shop items configuration
$SHOP_ITEMS = [
    'vehicles' => [
        ['id' => 'bicycle', 'price' => 0, 'currency' => 'coins', 'level' => 1],
        ['id' => 'scooter', 'price' => 500, 'currency' => 'coins', 'level' => 5],
        ['id' => 'car', 'price' => 2000, 'currency' => 'coins', 'level' => 10],
        ['id' => 'train', 'price' => 10000, 'currency' => 'coins', 'level' => 15],
        ['id' => 'plane', 'price' => 50000, 'currency' => 'coins', 'level' => 20]
    ],
    'clothes' => [
        ['id' => 'basic', 'price' => 0, 'currency' => 'coins', 'level' => 1],
        ['id' => 'explorer', 'price' => 300, 'currency' => 'coins', 'level' => 3],
        ['id' => 'scientist', 'price' => 800, 'currency' => 'coins', 'level' => 7],
        ['id' => 'pirate', 'price' => 1500, 'currency' => 'coins', 'level' => 10],
        ['id' => 'astronaut', 'price' => 5000, 'currency' => 'coins', 'level' => 15]
    ],
    'accessories' => [
        ['id' => 'backpack', 'price' => 100, 'currency' => 'coins', 'level' => 1],
        ['id' => 'hat', 'price' => 200, 'currency' => 'coins', 'level' => 3],
        ['id' => 'compass', 'price' => 500, 'currency' => 'coins', 'level' => 5]
    ]
];

try {
    switch ($action) {
        case 'purchaseItem':
            $itemId = $data['item_id'];
            $category = $data['category'];

            // Get user
            $user = new User($db);
            $user->id = $userId;
            $user->readById();

            // Check if already owned
            $transaction = new Transaction($db);
            $transaction->user_id = $userId;
            
            if ($transaction->isItemOwned($itemId, $category)) {
                $response['message'] = 'Item already owned';
                break;
            }

            // Find item in shop
            $item = null;
            foreach ($SHOP_ITEMS[$category] as $shopItem) {
                if ($shopItem['id'] === $itemId) {
                    $item = $shopItem;
                    break;
                }
            }

            if (!$item) {
                $response['message'] = 'Item not found';
                break;
            }

            // Check level requirement
            if ($user->level < $item['level']) {
                $response['message'] = 'Level too low';
                break;
            }

            // Check currency
            if ($item['currency'] === 'coins') {
                if ($user->coins < $item['price']) {
                    $response['message'] = 'Not enough coins';
                    break;
                }
                $user->removeCoins($item['price']);
            } else {
                if ($user->diamonds < $item['price']) {
                    $response['message'] = 'Not enough diamonds';
                    break;
                }
                $user->removeDiamonds($item['price']);
            }

            // Create transaction
            $transaction->type = 'purchase';
            $transaction->category = $category;
            $transaction->item_id = $itemId;
            $transaction->amount = $item['price'];
            $transaction->currency = $item['currency'];
            $transaction->description = "Purchased $itemId";

            if ($transaction->create()) {
                $response['success'] = true;
                $response['message'] = 'Purchase successful';
                $response['data'] = [
                    'coins' => $user->coins,
                    'diamonds' => $user->diamonds
                ];
            }
            break;

        case 'getOwnedItems':
            $transaction = new Transaction($db);
            $transaction->user_id = $userId;
            
            $ownedItems = $transaction->getOwnedItems();

            $response['success'] = true;
            $response['data'] = $ownedItems;
            break;

        case 'getPurchaseHistory':
            $transaction = new Transaction($db);
            $transaction->user_id = $userId;
            
            $history = $transaction->getPurchases();

            $response['success'] = true;
            $response['data'] = $history;
            break;

        default:
            $response['message'] = 'Invalid action';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
