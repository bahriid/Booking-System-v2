<?php

return [
    // Page title and breadcrumb
    'title' => 'Settings',
    'breadcrumb' => 'Settings',

    // Tab labels
    'tabs' => [
        'general' => 'General',
        'booking_rules' => 'Booking Rules',
        'email_notifications' => 'Email & Notifications',
        'language' => 'Language',
        'pickup_points' => 'Pickup Points',
        'users_admins' => 'Users & Admins',
        'voucher' => 'Voucher',
        'backup_logs' => 'Backup & Logs',
    ],

    // General Settings
    'general' => [
        'title' => 'General Settings',
        'company_name' => 'Company Name',
        'company_name_placeholder' => 'Your company name',
        'contact_email' => 'Contact Email',
        'contact_email_placeholder' => 'contact@example.com',
        'contact_phone' => 'Contact Phone',
        'contact_phone_placeholder' => '+39 000 000 000',
        'timezone' => 'Timezone',
        'currency' => 'Currency',
        'date_format' => 'Date Format',
        'company_address' => 'Company Address',
        'company_address_placeholder' => 'Full address for vouchers and documents',
    ],

    // Booking Rules
    'booking' => [
        'title' => 'Booking Rules',
        'cutoff_time' => 'Default Cut-off Time',
        'hours_before_departure' => 'hours before departure',
        'cutoff_help' => 'Partners cannot book after this time',
        'overbooking_duration' => 'Overbooking Request Duration',
        'hours' => 'hours',
        'overbooking_help' => 'Time for admin to approve/reject',
        'cancellation_policy' => 'Cancellation Policy',
        'free_cancellation' => 'Free Cancellation Until',
        'late_cancellation_penalty' => 'Late Cancellation Penalty',
        'percent_of_booking' => '% of booking value',
        'allow_overbooking' => 'Allow overbooking requests (suspended status with 2h approval)',
    ],

    // Email Settings
    'email' => [
        'title' => 'Email Configuration (SMTP)',
        'smtp_host' => 'SMTP Host',
        'smtp_host_placeholder' => 'smtp.example.com',
        'smtp_port' => 'SMTP Port',
        'smtp_username' => 'SMTP Username',
        'smtp_password' => 'SMTP Password',
        'smtp_password_placeholder' => 'Leave blank to keep current',
        'from_name' => 'From Name',
        'from_email' => 'From Email',
        'admin_notification_email' => 'Admin Notification Email',
        'send_test_email' => 'Send Test Email',
        'save_smtp' => 'Save SMTP Settings',
    ],

    // Notification Events
    'notifications' => [
        'title' => 'Notification Events',
        'event' => 'Event',
        'admin' => 'Admin',
        'partner' => 'Partner',
        'booking_confirmed' => 'New booking confirmed',
        'overbooking_requested' => 'Overbooking request (suspended)',
        'overbooking_resolved' => 'Overbooking approved/rejected',
        'booking_cancelled' => 'Booking cancelled',
        'booking_modified' => 'Booking modified',
        'tour_cancelled' => 'Tour cancelled',
        'save_notifications' => 'Save Notification Settings',
    ],

    // Language Settings
    'language' => [
        'title' => 'Language Settings',
        'info_message' => 'The application supports multiple languages. All labels, buttons, and messages can be displayed in the selected language.',
        'default_language' => 'Default Language',
        'default_language_help' => 'Default language for the entire application',
        'partner_language' => 'Partner Portal Language',
        'partner_language_help' => 'Language for the partner booking portal',
        'same_as_default' => 'Same as default',
        'available_languages' => 'Available Languages',
        'translated' => '100% translated',
        'active' => 'Active',
        'available' => 'Available',
        'save_language' => 'Save Language Settings',
    ],

    // Pickup Points
    'pickups' => [
        'title' => 'Pickup Points',
        'add' => 'Add Pickup Point',
        'name' => 'Name',
        'location' => 'Location',
        'default_time' => 'Default Time',
        'status' => 'Status',
        'actions' => 'Actions',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'no_pickups' => 'No pickup points found.',
        'create_one' => 'Create one',
    ],

    // Users
    'users' => [
        'title' => 'Admin Users',
        'add' => 'Add User',
        'user' => 'User',
        'role' => 'Role',
        'last_login' => 'Last Login',
        'status' => 'Status',
        'actions' => 'Actions',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'never' => 'Never',
        'edit' => 'Edit',
        'no_users' => 'No users found.',
        'create_one' => 'Create one',
    ],

    // Voucher Settings
    'voucher' => [
        'title' => 'Voucher Settings',
        'info' => 'Customize the text that appears on booking vouchers. These texts will be included in all voucher PDFs.',
        'header_text' => 'Voucher Header Text',
        'header_text_placeholder' => 'Welcome message or company tagline...',
        'header_text_help' => 'Displayed at the top of the voucher, after the company logo.',
        'operational_notes' => 'Operational Instructions',
        'operational_notes_placeholder' => 'Important instructions for guests, meeting points, what to bring...',
        'operational_notes_help' => 'Important information displayed prominently on the voucher. Use line breaks for multiple points.',
        'footer_text' => 'Voucher Footer Text',
        'footer_text_placeholder' => 'Thank you message, contact information...',
        'footer_text_help' => 'Displayed at the bottom of the voucher.',
    ],

    // Backup & Logs
    'backup' => [
        'title' => 'Database Backup',
        'create_now' => 'Create Backup Now',
        'info_message' => 'Automatic backups run every 6 hours (00:00, 06:00, 12:00, 18:00) and are retained for 28 days.',
        'recent' => 'Recent Backups',
        'date' => 'Date',
        'file' => 'File',
        'size' => 'Size',
        'status' => 'Status',
        'actions' => 'Actions',
        'success' => 'Success',
        'failed' => 'Failed',
        'download' => 'Download',
        'no_backups' => 'No backups found yet.',
    ],

    // Logs
    'logs' => [
        'title' => 'System Logs',
        'view_audit' => 'View Audit Logs',
        'view_email' => 'View Email Logs',
        'audit_logs' => 'Audit Logs',
        'audit_description' => 'Track all changes to bookings, tours, and partners',
        'email_logs' => 'Email Logs',
        'email_description' => 'View all sent emails and their delivery status',
        'backup_logs' => 'Backup Logs',
        'backup_description' => 'View backup history and download backups',
        'view' => 'View',
    ],

    // Buttons
    'save_changes' => 'Save Changes',
];
