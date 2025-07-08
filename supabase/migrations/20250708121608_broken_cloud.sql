-- Update notices table to support subheading and attachment types
-- Run this SQL to update your existing database

USE school_management_system;

-- Add missing columns to notices table if they don't exist
ALTER TABLE notices 
ADD COLUMN IF NOT EXISTS subheading VARCHAR(255) AFTER title,
ADD COLUMN IF NOT EXISTS attachment_type VARCHAR(50) AFTER attachment_path;

-- Update existing notices to have proper attachment types
UPDATE notices 
SET attachment_type = CASE 
    WHEN attachment_path IS NOT NULL AND attachment_path LIKE '%.pdf' THEN 'pdf'
    WHEN attachment_path IS NOT NULL AND (attachment_path LIKE '%.jpg' OR attachment_path LIKE '%.jpeg' OR attachment_path LIKE '%.png' OR attachment_path LIKE '%.gif') THEN 'image'
    WHEN attachment_path IS NOT NULL AND (attachment_path LIKE '%.doc' OR attachment_path LIKE '%.docx') THEN 'document'
    ELSE NULL
END
WHERE attachment_path IS NOT NULL AND attachment_type IS NULL;

-- Show table structure to verify
DESCRIBE notices;