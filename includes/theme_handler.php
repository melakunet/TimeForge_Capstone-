<?php
// Theme Handler - Save theme preference to session

require_once __DIR__ . '/../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'set_theme' && isset($_POST['theme'])) {
        $theme = $_POST['theme'];
        
        if (in_array($theme, ['light', 'dark'])) {
            $_SESSION['theme'] = $theme;
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid theme']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
