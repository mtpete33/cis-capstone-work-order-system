-- Seed data for Maintenance Work Order System

-- Roles
INSERT INTO roles ("roleName") VALUES 
    ('Admin'),
    ('Technician'),
    ('Requester')
ON CONFLICT ("roleName") DO NOTHING;

-- Statuses (sortOrder indicates workflow order, isTerminal marks end states)
INSERT INTO statuses ("statusName", "sortOrder", "isTerminal") VALUES 
    ('Submitted', 1, FALSE),
    ('Scheduled', 2, FALSE),
    ('In Progress', 3, FALSE),
    ('On Hold', 4, FALSE),
    ('Completed', 5, TRUE),
    ('Closed', 6, TRUE),
    ('Canceled', 7, TRUE)
ON CONFLICT DO NOTHING;

-- Priorities
INSERT INTO priorities ("priorityName", "sortOrder") VALUES 
    ('Low', 1),
    ('Medium', 2),
    ('High', 3),
    ('Emergency', 4)
ON CONFLICT DO NOTHING;

-- Permissions
INSERT INTO permissions ("permissionKey", "description") VALUES 
    ('CREATE_WORKORDER', 'Can create new work orders'),
    ('VIEW_ALL_WORKORDERS', 'Can view all work orders'),
    ('VIEW_ASSIGNED_WORKORDERS', 'Can view assigned work orders'),
    ('VIEW_OWN_WORKORDERS', 'Can view own submitted work orders'),
    ('ASSIGN_TECH', 'Can assign technicians to work orders'),
    ('CHANGE_STATUS', 'Can change work order status'),
    ('ADD_COMMENT', 'Can add comments to work orders')
ON CONFLICT ("permissionKey") DO NOTHING;

-- Role Permissions mapping
-- Admin gets: CREATE_WORKORDER, VIEW_ALL_WORKORDERS, ASSIGN_TECH, CHANGE_STATUS, ADD_COMMENT
INSERT INTO role_permissions ("roleID", "permissionID")
SELECT r."roleID", p."permissionID"
FROM roles r, permissions p
WHERE r."roleName" = 'Admin' 
AND p."permissionKey" IN ('CREATE_WORKORDER', 'VIEW_ALL_WORKORDERS', 'ASSIGN_TECH', 'CHANGE_STATUS', 'ADD_COMMENT')
ON CONFLICT DO NOTHING;

-- Technician gets: VIEW_ASSIGNED_WORKORDERS, CHANGE_STATUS, ADD_COMMENT
INSERT INTO role_permissions ("roleID", "permissionID")
SELECT r."roleID", p."permissionID"
FROM roles r, permissions p
WHERE r."roleName" = 'Technician' 
AND p."permissionKey" IN ('VIEW_ASSIGNED_WORKORDERS', 'CHANGE_STATUS', 'ADD_COMMENT')
ON CONFLICT DO NOTHING;

-- Requester gets: CREATE_WORKORDER, VIEW_OWN_WORKORDERS, ADD_COMMENT
INSERT INTO role_permissions ("roleID", "permissionID")
SELECT r."roleID", p."permissionID"
FROM roles r, permissions p
WHERE r."roleName" = 'Requester' 
AND p."permissionKey" IN ('CREATE_WORKORDER', 'VIEW_OWN_WORKORDERS', 'ADD_COMMENT')
ON CONFLICT DO NOTHING;

-- Sample Locations (school district buildings)
INSERT INTO locations ("locationName", "isActive") VALUES 
    ('Central High School', TRUE),
    ('Lincoln Elementary', TRUE),
    ('Washington Middle School', TRUE),
    ('District Admin Building', TRUE),
    ('Sports Complex', TRUE)
ON CONFLICT DO NOTHING;

-- Sample Departments
INSERT INTO departments ("departmentName", "isActive") VALUES 
    ('HVAC', TRUE),
    ('Electrical', TRUE),
    ('Plumbing', TRUE),
    ('Grounds', TRUE),
    ('General Maintenance', TRUE)
ON CONFLICT DO NOTHING;

-- Sample Categories
INSERT INTO categories ("categoryName", "departmentID", "isActive")
SELECT 'Heating Issue', d."departmentID", TRUE FROM departments d WHERE d."departmentName" = 'HVAC'
ON CONFLICT DO NOTHING;

INSERT INTO categories ("categoryName", "departmentID", "isActive")
SELECT 'Cooling Issue', d."departmentID", TRUE FROM departments d WHERE d."departmentName" = 'HVAC'
ON CONFLICT DO NOTHING;

INSERT INTO categories ("categoryName", "departmentID", "isActive")
SELECT 'Light Repair', d."departmentID", TRUE FROM departments d WHERE d."departmentName" = 'Electrical'
ON CONFLICT DO NOTHING;

INSERT INTO categories ("categoryName", "departmentID", "isActive")
SELECT 'Outlet Issue', d."departmentID", TRUE FROM departments d WHERE d."departmentName" = 'Electrical'
ON CONFLICT DO NOTHING;

INSERT INTO categories ("categoryName", "departmentID", "isActive")
SELECT 'Leak Repair', d."departmentID", TRUE FROM departments d WHERE d."departmentName" = 'Plumbing'
ON CONFLICT DO NOTHING;

-- Demo Users (passwords: admin123, tech123, requester123)
-- These hashes are generated with password_hash() using PASSWORD_DEFAULT
INSERT INTO users ("userName", "email", "pwHash", "roleID")
SELECT 'Admin User', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', r."roleID"
FROM roles r WHERE r."roleName" = 'Admin'
ON CONFLICT ("email") DO NOTHING;

INSERT INTO users ("userName", "email", "pwHash", "roleID")
SELECT 'Tech User', 'tech@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', r."roleID"
FROM roles r WHERE r."roleName" = 'Technician'
ON CONFLICT ("email") DO NOTHING;

INSERT INTO users ("userName", "email", "pwHash", "roleID")
SELECT 'Requester User', 'requester@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', r."roleID"
FROM roles r WHERE r."roleName" = 'Requester'
ON CONFLICT ("email") DO NOTHING;
