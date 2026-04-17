-- Invoicing schema: invoices table and tax_rate on projects

CREATE TABLE IF NOT EXISTS invoices (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  project_id     INT NOT NULL,
  client_id      INT NOT NULL,
  invoice_number VARCHAR(50) UNIQUE NOT NULL,
  issue_date     DATE NOT NULL,
  due_date       DATE NOT NULL,
  tax_rate       DECIMAL(5,2) DEFAULT 0.00,
  subtotal       DECIMAL(10,2) NOT NULL,
  tax_amount     DECIMAL(10,2) NOT NULL,
  total_amount   DECIMAL(10,2) NOT NULL,
  notes          TEXT DEFAULT NULL,
  status         ENUM('draft','sent','paid') DEFAULT 'draft',
  created_by     INT NOT NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id)  REFERENCES clients(id)  ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id)    ON DELETE CASCADE,
  INDEX idx_project  (project_id),
  INDEX idx_client   (client_id),
  INDEX idx_status   (status),
  INDEX idx_created  (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tax_rate on projects lets each project carry its own default tax rate
-- so the invoice generator can pre-fill it without manual entry.
ALTER TABLE projects
  ADD COLUMN IF NOT EXISTS tax_rate DECIMAL(5,2) DEFAULT 0.00
  COMMENT 'Default tax percentage for invoices on this project';
