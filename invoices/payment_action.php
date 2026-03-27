<?php
/*
 * Phase 8 — Payment Action Handler
 * Handles all invoice lifecycle transitions via POST.
 * Only admins may change invoice status; clients may add feedback.
 *
 * Valid status flow:
 *   draft → sent → viewed → overdue (auto-cron) → partial → paid → completed
 *   Any status → cancelled  (admin only)
 *
 * POST params:
 *   invoice_id      int      required
 *   action          string   required — see $allowed_actions below
 *   payment_method  string   optional (for partial / paid transitions)
 *   payment_reference string optional
 *   payment_notes   string   optional
 *   partial_amount  float    required when action = record_partial
 *   client_feedback string   required when action = add_feedback
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

if (!isLoggedIn()) {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

$user_id    = $_SESSION['user_id'];
$role       = $_SESSION['role'];
$invoice_id = (int)($_POST['invoice_id'] ?? 0);
$action     = trim($_POST['action'] ?? '');

if (!$invoice_id) {
    setFlash('error', 'Invalid invoice.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

// ------------------------------------------------------------------
// Load the invoice
// ------------------------------------------------------------------
try {
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $invoice_id]);
    $invoice = $stmt->fetch();
} catch (PDOException $e) {
    error_log('payment_action.php load: ' . $e->getMessage());
    setFlash('error', 'Database error. Please try again.');
    header("Location: /TimeForge_Capstone/invoices/view.php?id={$invoice_id}");
    exit;
}

if (!$invoice) {
    setFlash('error', 'Invoice not found.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

$redirect = "/TimeForge_Capstone/invoices/view.php?id={$invoice_id}";

// ------------------------------------------------------------------
// Client feedback action — clients allowed
// ------------------------------------------------------------------
if ($action === 'add_feedback') {
    $feedback = trim($_POST['client_feedback'] ?? '');
    if ($feedback === '') {
        setFlash('error', 'Feedback cannot be empty.');
        header("Location: {$redirect}");
        exit;
    }

    // Clients may only add feedback to their own invoices
    if ($role === 'client') {
        $check = $pdo->prepare("
            SELECT inv.id FROM invoices inv
            INNER JOIN clients c ON c.id = inv.client_id
            WHERE inv.id = :id AND c.user_id = :uid LIMIT 1
        ");
        $check->execute([':id' => $invoice_id, ':uid' => $user_id]);
        if (!$check->fetch()) {
            setFlash('error', 'Access denied.');
            header("Location: {$redirect}");
            exit;
        }
    } elseif ($role !== 'admin') {
        setFlash('error', 'Access denied.');
        header("Location: {$redirect}");
        exit;
    }

    try {
        $upd = $pdo->prepare("UPDATE invoices SET client_feedback = :fb WHERE id = :id");
        $upd->execute([':fb' => $feedback, ':id' => $invoice_id]);
        setFlash('success', 'Feedback saved.');
    } catch (PDOException $e) {
        error_log('payment_action.php feedback: ' . $e->getMessage());
        setFlash('error', 'Could not save feedback.');
    }

    header("Location: {$redirect}");
    exit;
}

// ------------------------------------------------------------------
// All remaining actions are admin-only
// ------------------------------------------------------------------
if ($role !== 'admin') {
    setFlash('error', 'Access denied.');
    header("Location: {$redirect}");
    exit;
}

$now = date('Y-m-d H:i:s');
$payment_method    = trim($_POST['payment_method']    ?? '');
$payment_reference = trim($_POST['payment_reference'] ?? '');
$payment_notes     = trim($_POST['payment_notes']     ?? '');

// ------------------------------------------------------------------
// Route actions → SQL updates
// ------------------------------------------------------------------
$allowed_actions = [
    'mark_sent',       // draft     → sent
    'mark_viewed',     // sent/overdue → viewed
    'mark_overdue',    // sent/viewed  → overdue
    'record_partial',  // any        → partial
    'mark_paid',       // any        → paid
    'mark_completed',  // paid       → completed
    'mark_cancelled',  // any        → cancelled
    'save_notes',      // update payment_notes only, no status change
];

if (!in_array($action, $allowed_actions)) {
    setFlash('error', 'Unknown action.');
    header("Location: {$redirect}");
    exit;
}

try {
    switch ($action) {

        // ── Mark Sent ────────────────────────────────────────────
        case 'mark_sent':
            $pdo->prepare("
                UPDATE invoices
                SET status = 'sent', sent_at = COALESCE(sent_at, :now),
                    payment_notes = IF(:notes != '', :notes2, payment_notes)
                WHERE id = :id
            ")->execute([':now' => $now, ':notes' => $payment_notes,
                         ':notes2' => $payment_notes, ':id' => $invoice_id]);
            setFlash('success', 'Invoice marked as Sent. Client will be notified on next login.');
            break;

        // ── Mark Viewed ──────────────────────────────────────────
        case 'mark_viewed':
            $pdo->prepare("
                UPDATE invoices
                SET status = 'viewed', viewed_at = COALESCE(viewed_at, :now)
                WHERE id = :id
            ")->execute([':now' => $now, ':id' => $invoice_id]);
            setFlash('success', 'Invoice marked as Viewed.');
            break;

        // ── Mark Overdue ─────────────────────────────────────────
        case 'mark_overdue':
            $pdo->prepare("
                UPDATE invoices SET status = 'overdue',
                    payment_notes = IF(:notes != '', :notes2, payment_notes)
                WHERE id = :id
            ")->execute([':notes' => $payment_notes, ':notes2' => $payment_notes,
                         ':id' => $invoice_id]);
            setFlash('warning', 'Invoice marked as Overdue.');
            break;

        // ── Record Partial Payment ───────────────────────────────
        case 'record_partial':
            $partial = (float)($_POST['partial_amount'] ?? 0);
            if ($partial <= 0 || $partial >= $invoice['total_amount']) {
                setFlash('error', 'Partial amount must be greater than $0 and less than the invoice total ($' .
                         number_format($invoice['total_amount'], 2) . ').');
                break;
            }
            $pdo->prepare("
                UPDATE invoices
                SET status = 'partial',
                    partial_amount    = :amt,
                    payment_method    = IF(:method != '', :method2, payment_method),
                    payment_reference = IF(:ref != '', :ref2, payment_reference),
                    payment_notes     = IF(:notes != '', :notes2, payment_notes)
                WHERE id = :id
            ")->execute([
                ':amt'    => $partial,
                ':method' => $payment_method,  ':method2' => $payment_method,
                ':ref'    => $payment_reference, ':ref2'   => $payment_reference,
                ':notes'  => $payment_notes,    ':notes2'  => $payment_notes,
                ':id'     => $invoice_id,
            ]);
            setFlash('success', 'Partial payment of $' . number_format($partial, 2) . ' recorded.');
            break;

        // ── Mark Paid ────────────────────────────────────────────
        case 'mark_paid':
            $pdo->prepare("
                UPDATE invoices
                SET status = 'paid',
                    paid_at           = COALESCE(paid_at, :now),
                    payment_method    = IF(:method != '', :method2, payment_method),
                    payment_reference = IF(:ref != '', :ref2, payment_reference),
                    payment_notes     = IF(:notes != '', :notes2, payment_notes)
                WHERE id = :id
            ")->execute([
                ':now'    => $now,
                ':method' => $payment_method,   ':method2' => $payment_method,
                ':ref'    => $payment_reference, ':ref2'   => $payment_reference,
                ':notes'  => $payment_notes,    ':notes2'  => $payment_notes,
                ':id'     => $invoice_id,
            ]);
            setFlash('success', 'Invoice marked as Paid. 🎉');
            break;

        // ── Mark Completed ───────────────────────────────────────
        case 'mark_completed':
            if ($invoice['status'] !== 'paid') {
                setFlash('error', 'Invoice must be Paid before marking Completed.');
                break;
            }
            $pdo->prepare("UPDATE invoices SET status = 'completed' WHERE id = :id")
                ->execute([':id' => $invoice_id]);
            setFlash('success', 'Invoice completed and archived.');
            break;

        // ── Cancel ───────────────────────────────────────────────
        case 'mark_cancelled':
            $pdo->prepare("
                UPDATE invoices
                SET status = 'cancelled',
                    payment_notes = IF(:notes != '', :notes2, payment_notes)
                WHERE id = :id
            ")->execute([':notes' => $payment_notes, ':notes2' => $payment_notes,
                         ':id' => $invoice_id]);
            setFlash('warning', 'Invoice cancelled.');
            break;

        // ── Save Notes Only ──────────────────────────────────────
        case 'save_notes':
            $pdo->prepare("
                UPDATE invoices
                SET payment_notes     = :notes,
                    payment_method    = IF(:method != '', :method2, payment_method),
                    payment_reference = IF(:ref != '', :ref2, payment_reference)
                WHERE id = :id
            ")->execute([
                ':notes'  => $payment_notes,
                ':method' => $payment_method,   ':method2' => $payment_method,
                ':ref'    => $payment_reference, ':ref2'   => $payment_reference,
                ':id'     => $invoice_id,
            ]);
            setFlash('success', 'Notes saved.');
            break;
    }
} catch (PDOException $e) {
    error_log('payment_action.php action=' . $action . ': ' . $e->getMessage());
    setFlash('error', 'Database error. Please try again.');
}

header("Location: {$redirect}");
exit;
