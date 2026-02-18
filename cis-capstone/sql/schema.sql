-- Maintenance Work Order System Schema

-- Roles table
CREATE TABLE IF NOT EXISTS roles (
    "roleID" SERIAL PRIMARY KEY,
    "roleName" VARCHAR(50) NOT NULL UNIQUE
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    "userID" SERIAL PRIMARY KEY,
    "userName" VARCHAR(100) NOT NULL,
    "email" VARCHAR(255) NOT NULL UNIQUE,
    "pwHash" VARCHAR(255) NOT NULL,
    "roleID" INTEGER NOT NULL REFERENCES roles("roleID"),
    "createdAt" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Permissions table
CREATE TABLE IF NOT EXISTS permissions (
    "permissionID" SERIAL PRIMARY KEY,
    "permissionKey" VARCHAR(50) NOT NULL UNIQUE,
    "description" TEXT
);

-- Role-Permission mapping
CREATE TABLE IF NOT EXISTS role_permissions (
    "roleID" INTEGER NOT NULL REFERENCES roles("roleID"),
    "permissionID" INTEGER NOT NULL REFERENCES permissions("permissionID"),
    PRIMARY KEY ("roleID", "permissionID")
);

-- Locations table
CREATE TABLE IF NOT EXISTS locations (
    "locationID" SERIAL PRIMARY KEY,
    "locationName" VARCHAR(200) NOT NULL,
    "isActive" BOOLEAN DEFAULT TRUE
);

-- Priorities table
CREATE TABLE IF NOT EXISTS priorities (
    "priorityID" SERIAL PRIMARY KEY,
    "priorityName" VARCHAR(50) NOT NULL,
    "sortOrder" INTEGER DEFAULT 0
);

-- Statuses table
CREATE TABLE IF NOT EXISTS statuses (
    "statusID" SERIAL PRIMARY KEY,
    "statusName" VARCHAR(50) NOT NULL,
    "sortOrder" INTEGER DEFAULT 0,
    "isTerminal" BOOLEAN DEFAULT FALSE
);

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    "departmentID" SERIAL PRIMARY KEY,
    "departmentName" VARCHAR(100) NOT NULL,
    "isActive" BOOLEAN DEFAULT TRUE
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    "categoryID" SERIAL PRIMARY KEY,
    "categoryName" VARCHAR(100) NOT NULL,
    "departmentID" INTEGER REFERENCES departments("departmentID"),
    "isActive" BOOLEAN DEFAULT TRUE
);

-- Work Orders table
CREATE TABLE IF NOT EXISTS work_orders (
    "workOrderID" SERIAL PRIMARY KEY,
    "title" VARCHAR(255) NOT NULL,
    "description" TEXT,
    "locationID" INTEGER NOT NULL REFERENCES locations("locationID"),
    "priorityID" INTEGER NOT NULL REFERENCES priorities("priorityID"),
    "currentStatusID" INTEGER NOT NULL REFERENCES statuses("statusID"),
    "departmentID" INTEGER REFERENCES departments("departmentID"),
    "categoryID" INTEGER REFERENCES categories("categoryID"),
    "submittedByUserID" INTEGER NOT NULL REFERENCES users("userID"),
    "assignedToUserID" INTEGER REFERENCES users("userID"),
    "createdAt" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "scheduledFor" TIMESTAMP,
    "completedAt" TIMESTAMP,
    "closedAt" TIMESTAMP,
    "lastUpdatedAt" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Work Order Status History
CREATE TABLE IF NOT EXISTS work_order_status_history (
    "statusHistoryID" SERIAL PRIMARY KEY,
    "workOrderID" INTEGER NOT NULL REFERENCES work_orders("workOrderID"),
    "oldStatusID" INTEGER REFERENCES statuses("statusID"),
    "newStatusID" INTEGER NOT NULL REFERENCES statuses("statusID"),
    "changedByUserID" INTEGER NOT NULL REFERENCES users("userID"),
    "changedAt" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "note" TEXT
);

-- Work Order Comments
CREATE TABLE IF NOT EXISTS work_order_comments (
    "commentID" SERIAL PRIMARY KEY,
    "workOrderID" INTEGER NOT NULL REFERENCES work_orders("workOrderID"),
    "commentText" TEXT NOT NULL,
    "createdByUserID" INTEGER NOT NULL REFERENCES users("userID"),
    "createdAt" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "isInternal" BOOLEAN DEFAULT FALSE
);

-- Attachments (future use)
CREATE TABLE IF NOT EXISTS attachments (
    "attachmentID" SERIAL PRIMARY KEY,
    "workOrderID" INTEGER NOT NULL REFERENCES work_orders("workOrderID"),
    "fileName" VARCHAR(255) NOT NULL,
    "fileUrl" TEXT NOT NULL,
    "mimeType" VARCHAR(100),
    "uploadedByUserID" INTEGER NOT NULL REFERENCES users("userID"),
    "uploadedAt" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Indexes for common queries
CREATE INDEX IF NOT EXISTS idx_work_orders_status ON work_orders("currentStatusID");
CREATE INDEX IF NOT EXISTS idx_work_orders_assigned ON work_orders("assignedToUserID");
CREATE INDEX IF NOT EXISTS idx_work_orders_submitted ON work_orders("submittedByUserID");
CREATE INDEX IF NOT EXISTS idx_work_orders_location ON work_orders("locationID");
CREATE INDEX IF NOT EXISTS idx_status_history_workorder ON work_order_status_history("workOrderID");
CREATE INDEX IF NOT EXISTS idx_comments_workorder ON work_order_comments("workOrderID");
