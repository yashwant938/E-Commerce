<?php
/** Build a parameterized listing search; return false for malformed filters. */
function buildPropertySearch($types, $minBudget, $maxBudget, $available, $locations, $amenities, $search) {
    foreach ([$types, $locations, $amenities] as $values) {
        if (!is_array($values) || count($values) === 0 || count($values) > 100) return false;
        foreach ($values as $value) {
            if (!is_scalar($value) || !preg_match('/\A[0-9]+\z/', (string) $value) || strlen((string) $value) > 9) return false;
        }
    }
    foreach ($types as $type) if (!in_array((string) $type, ['0', '1'], true)) return false;
    foreach ([$minBudget, $maxBudget] as $budget) {
        if (!is_scalar($budget) || ((string) $budget !== '' && (!is_numeric($budget) || (float) $budget < 0))) return false;
    }
    if (!is_string($available) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $available) || !is_string($search)) return false;
    $params = ['avi_date' => $available, 'search' => '%' . $search . '%'];
    $slots = function ($values, $prefix) use (&$params) {
        $names = [];
        foreach (array_values($values) as $i => $value) {
            $key = $prefix . $i;
            $names[] = ':' . $key;
            $params[$key] = (int) $value;
        }
        return implode(',', $names);
    };
    $locationSlots = $slots($locations, 'location');
    $typeSlots = $slots($types, 'type');
    $amenityClauses = [];
    foreach (array_values($amenities) as $i => $amenity) {
        $key = 'amenity' . $i;
        $amenityClauses[] = 'p.amenity LIKE :' . $key;
        $params[$key] = '%' . $amenity . '%';
    }
    $sql = 'SELECT p.*, l.location_name AS `location`, p.location AS location_id '
        . 'FROM property p LEFT JOIN locations l ON l.location_id = p.location '
        . 'WHERE p.location IN (' . $locationSlots . ') AND (' . implode(' OR ', $amenityClauses) . ') '
        . 'AND p.property_type IN (' . $typeSlots . ') AND DATE(p.available_from) <= :avi_date '
        . 'AND p.address LIKE :search AND p.isActive=1';
    if ((string) $minBudget !== '') { $sql .= ' AND p.price >= :min_budget'; $params['min_budget'] = $minBudget; }
    if ((string) $maxBudget !== '') { $sql .= ' AND p.price <= :max_budget'; $params['max_budget'] = $maxBudget; }
    return [$sql . ' ORDER BY p.property_id DESC', $params];
}
