<?php
// Synthetic database/bootstrap doubles for the standalone admin handlers.
function recordAdminTestEvent($operation, $id = null) {
    $path = dirname(__DIR__, 2) . '/state.json';
    $state = json_decode(file_get_contents($path), true);
    $state[] = ['operation' => $operation, 'id' => $id];
    file_put_contents($path, json_encode($state), LOCK_EX);
}
class Database {
    public static function getInstance() { recordAdminTestEvent('database'); return new stdClass(); }
}
class Functions {
    public static function constructStatic($db) {}
    public static function filterInput($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
    public static function date_time_disp_format($value) { return $value; }
}
class User {
    public static function constructStatic($db) {}
    public static function toggleActive($id) { recordAdminTestEvent('user-status', $id); return true; }
    public static function fetchAllUserWithAuth() {
        return array_map(function ($id) {
            return ['user_id' => $id, 'full_name' => 'Synthetic user', 'email' => 'fixture@example.invalid',
                'contracts' => 0, 'user_ip' => '127.0.0.1', 'loggedin_on' => '2026-01-01', 'isActive' => 1];
        }, [4242, 4243]);
    }
}
class Property {
    public static function constructStatic($db) {}
    public static function removeAdmin($id) { recordAdminTestEvent('property-status', $id); return true; }
    public static function fetchAllAdminProperty() {
        $row = array_fill_keys(['owner_name', 'created_on', 'modified_name', 'modified_on', 'location',
            'property_size', 'price', 'property_type', 'description', 'images', 'address', 'isSold',
            'buyer_name', 'available_from', 'buy_date', 'property_ownership', 'lease_months'], 'synthetic');
        $row['property_id'] = 99;
        $row['isActive'] = 1;
        return [$row];
    }
}
