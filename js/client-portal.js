/**
 * client-portal.js
 * JavaScript for Client Portal pages (dashboard & project report).
 * Depends on: theme.js (already loaded globally via header)
 */

'use strict';

/**
 * Trigger the browser's native print dialog.
 * Called from the "Print Report" button in project_report.php
 */
function printReport() {
    window.print();
}
