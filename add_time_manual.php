<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    $start_date = $_POST['start_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $duration_hours = filter_input(INPUT_POST, 'duration_hours', FILTER_VALIDATE_FLOAT);
    $description = trim($_POST['description'] ?? '');
    
    // Authorization
    if (!hasRole('admin') && !hasRole('freelancer')) {
        setFlash('error', 'Unauthorized action.');
        header("Location: index.php");
        exit;
    }

    // Validation
    if (!$project_id || empty($start_date) || empty($start_time) || !$duration_hours || empty($description)) {
        setFlash('error', 'Please fill in all fields correctly.');
        if ($project_id) {
            header("Location: project_details.php?id=" . $project_id);
        } else {
            header("Location: index.php");
        }
        exit;
    }

    // Calculate Timestamps
    $start_datetime_str = "$start_date $start_time";
    $start_datetime = new DateTime($start_datetime_str);
    
    // Clone start time to calculate end time based on duration
    $end_datetime = clone $start_datetime;
    // Convert hours (e.g. 1.5) to minutes (90)
    $minutes = round($duration_hours * 60);
    $end_datetime->modify("+$minutes minutes");
    
    // Prepare Data
    $start_ts = $start_datetime->format('Y-m-d H:i:s');
    $end_ts = $end_datetime->format('Y-m-d H:i:s');
    $total_seconds = $minutes * 60;
    
    // Status Logic: Admin = Approved, Freelancer = Pending
    $status = hasRole('admin') ? 'approved' : 'pending';
    $entry_type = 'manual';

    try {
        $sql = "INSERT INTO time_entries (project_id, user_id, start_time, end_time, total_seconds, description, status, entry_type) 
                VALUES (:pid, :uid, :start, :end, :seconds, :desc, :status, :type)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pid' => $project_id,
            ':uid' => $_SESSION['user_id'],
            ':start' => $start_ts,
            ':end' => $end_ts,
            ':seconds' => $total_seconds,
            ':desc' => $description,
            ':status' => $status,
            ':type' => $entry_type
        ]);

        $msg = hasRole('admin') 
            ? 'Time entry added successfully.' 
            : 'Time entry submitted for approval.';
            
        setFlash('success', $msg);

    } catch (PDOException $e) {
        error_log("Manual Time Entry Error: " . $e->getMessage());
        setFlash('error', 'Database error. Could not save entry.');
    }

    header("Location: project_details.php?id=" . $project_id);
    exit;
}
