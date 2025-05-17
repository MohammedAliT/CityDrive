<?php
// config.php - Updated for Firestore
$project_id = "citydrive-82ff0";
$api_key = "AIzaSyDLg7mTUTter3RHzmJocz9EPAnAWnpypSM";
function firestoreRequest($collection, $method = 'GET', $document = null, $data = null) {
    global $project_id, $api_key;
    
    $base_url = "https://firestore.googleapis.com/v1/projects/$project_id/databases/(default)/documents/";
    
    $url = $base_url . $collection;
    if ($document) {
        $url .= "/" . $document;
    }
    $url .= "?key=" . $api_key;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['fields' => $data]));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Helper function to format Firestore data for display
function formatFirestoreData($firestoreResponse) {
    if (!isset($firestoreResponse['documents'])) return [];
    
    $result = [];
    foreach ($firestoreResponse['documents'] as $doc) {
        $id = basename($doc['name']);
        $fields = [];
        foreach ($doc['fields'] as $field => $value) {
            // Handle different Firestore value types
            if (isset($value['stringValue'])) {
                $fields[$field] = $value['stringValue'];
            } elseif (isset($value['integerValue'])) {
                $fields[$field] = (int)$value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $fields[$field] = (float)$value['doubleValue'];
            } elseif (isset($value['timestampValue'])) {
                $fields[$field] = strtotime($value['timestampValue']);
            } elseif (isset($value['mapValue'])) {
                $fields[$field] = $value['mapValue'];
            } else {
                $fields[$field] = $value;
            }
        }
        $result[$id] = $fields;
    }
    return $result;
}

// Helper to get a single document
function getFirestoreDocument($collection, $documentId) {
    $response = firestoreRequest($collection, 'GET', $documentId);
    if (isset($response['fields'])) {
        $result = [];
        foreach ($response['fields'] as $field => $value) {
            // Handle different Firestore value types
            if (isset($value['stringValue'])) {
                $result[$field] = $value['stringValue'];
            } elseif (isset($value['integerValue'])) {
                $result[$field] = (int)$value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $result[$field] = (float)$value['doubleValue'];
            } elseif (isset($value['timestampValue'])) {
                $result[$field] = strtotime($value['timestampValue']);
            } else {
                $result[$field] = $value;
            }
        }
        return $result;
    }
    return null;
}


// --- Firebase Sign In ---
function firebaseSignIn($email, $password, $api_key) {
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$api_key";
    $data = [
        'email' => $email,
        'password' => $password,
        'returnSecureToken' => true
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// --- Is Admin Check ---
function isAdmin($uid) {
    $userDoc = getFirestoreDocument("users", $uid);
    return isset($userDoc['role']) && $userDoc['role'] === 'admin';
}
?>