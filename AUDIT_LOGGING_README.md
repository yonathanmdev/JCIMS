# Audit Logging Implementation

This document explains how to implement audit logging in the JCIMS system.

## Overview

The audit logging system tracks user actions such as:
- User creation, updates, and deletions
- Login and logout events
- Organization and branch management
- Other administrative actions

## Components

### 1. Database Table
Run the following SQL to create the audit_logs table:

```sql
-- Create audit_logs table
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(36) NULL, -- UUID of the user performing the action
    action VARCHAR(255) NOT NULL, -- e.g., 'user_created', 'user_updated', 'login', etc.
    entity_type VARCHAR(100) NOT NULL, -- e.g., 'user', 'organization', 'branch'
    entity_id VARCHAR(36) NULL, -- ID of the affected entity
    old_values JSON NULL, -- Previous values (for updates)
    new_values JSON NULL, -- New values
    ip_address VARCHAR(45) NULL, -- IPv4/IPv6
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);
```

### 2. Dependencies
The system uses Monolog for file logging. It has been added to composer.json.

### 3. Models and Helpers

#### AuditLog Model (`src/Models/AuditLog.php`)
- Handles database operations for audit logs
- Logs to both database and file
- Provides methods for retrieving logs and statistics

#### AuditHelper (`src/Helpers/AuditHelper.php`)
- Provides easy-to-use methods for logging common actions
- Methods like `logUserCreation()`, `logLogin()`, etc.

### 4. Controllers

#### AuditController (`src/Controllers/AuditController.php`)
- `showAuditLogs()`: Displays audit logs with filtering
- `getAuditStats()`: Returns statistics via AJAX

#### Modified Controllers
- **UserController**: Logs user creation
- **AuthController**: Logs login/logout events

### 5. Routes
Added to `src/Routes/Yoniroutes.php`:
- `audit-logs`: View audit logs (system_admin only)
- `audit-stats`: Get audit statistics

### 6. View
- `views/audit-logs.php`: Interface for viewing and filtering audit logs

## Usage

### Logging Actions
In any controller, use the AuditHelper:

```php
use App\Helpers\AuditHelper;

// Log user creation
AuditHelper::logUserCreation($userId, $userData);

// Log user update
AuditHelper::logUserUpdate($userId, $oldData, $newData);

// Log login
AuditHelper::logLogin($userId, true); // success
AuditHelper::logLogin(null, false); // failed

// Log custom action
AuditHelper::log('custom_action', 'entity_type', $entityId, $oldValues, $newValues);
```

### Viewing Logs
Access `/audit-logs` (requires system_admin role) to view audit logs with filtering options.

## Security Considerations

1. **Access Control**: Only system administrators can view audit logs
2. **Data Retention**: Consider implementing log rotation/cleanup
3. **Sensitive Data**: Avoid logging passwords or other sensitive information
4. **Performance**: Audit logging is synchronous - monitor database performance

## File Locations

- Logs are stored in: `storage/logs/audit.log`
- Database table: `audit_logs`
- Configuration: Automatic initialization in BaseController

## Next Steps

1. Run the SQL script to create the audit_logs table
2. Test the logging functionality
3. Add logging to other controllers as needed
4. Implement log retention policies
5. Add more detailed views for log entries