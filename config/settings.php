<?php
/**
 * config/settings.php
 * Loads company settings from company_settings table into a simple array.
 * Usage:  $s = getCompanySettings($pdo, $company_id);
 *         $s['invoice_tax_rate']   → '13'
 *         getSetting($pdo, $company_id, 'invoice_tax_rate', '13')
 */

/**
 * Return all settings for a company as [key => val].
 * Returns an empty array if none found — callers should use defaults.
 */
function getCompanySettings(PDO $pdo, int $company_id): array {
    $stmt = $pdo->prepare("SELECT setting_key, setting_val FROM company_settings WHERE company_id = :cid");
    $stmt->execute([':cid' => $company_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) $out[$r['setting_key']] = $r['setting_val'];
    return $out;
}

/**
 * Read a single setting, with a fallback default.
 */
function getSetting(PDO $pdo, int $company_id, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT setting_val FROM company_settings WHERE company_id = :cid AND setting_key = :k LIMIT 1");
    $stmt->execute([':cid' => $company_id, ':k' => $key]);
    $val = $stmt->fetchColumn();
    return ($val !== false && $val !== null) ? (string)$val : $default;
}

/**
 * Upsert a single setting.
 */
function saveSetting(PDO $pdo, int $company_id, string $key, string $value): void {
    $stmt = $pdo->prepare("
        INSERT INTO company_settings (company_id, setting_key, setting_val)
        VALUES (:cid, :k, :v)
        ON DUPLICATE KEY UPDATE setting_val = VALUES(setting_val), updated_at = NOW()
    ");
    $stmt->execute([':cid' => $company_id, ':k' => $key, ':v' => $value]);
}
