<?php

return [
    // Page title and breadcrumb
    'title' => 'Impostazioni',
    'breadcrumb' => 'Impostazioni',

    // Tab labels
    'tabs' => [
        'general' => 'Generali',
        'booking_rules' => 'Regole Prenotazione',
        'email_notifications' => 'Email e Notifiche',
        'language' => 'Lingua',
        'pickup_points' => 'Punti di Ritiro',
        'users_admins' => 'Utenti e Admin',
        'voucher' => 'Voucher',
        'backup_logs' => 'Backup e Log',
    ],

    // General Settings
    'general' => [
        'title' => 'Impostazioni Generali',
        'company_name' => 'Nome Azienda',
        'company_name_placeholder' => 'Il nome della tua azienda',
        'contact_email' => 'Email di Contatto',
        'contact_email_placeholder' => 'contatto@esempio.com',
        'contact_phone' => 'Telefono di Contatto',
        'contact_phone_placeholder' => '+39 000 000 000',
        'timezone' => 'Fuso Orario',
        'currency' => 'Valuta',
        'date_format' => 'Formato Data',
        'company_address' => 'Indirizzo Azienda',
        'company_address_placeholder' => 'Indirizzo completo per voucher e documenti',
    ],

    // Booking Rules
    'booking' => [
        'title' => 'Regole Prenotazione',
        'cutoff_time' => 'Termine Prenotazione Predefinito',
        'hours_before_departure' => 'ore prima della partenza',
        'cutoff_help' => 'I partner non possono prenotare dopo questo orario',
        'overbooking_duration' => 'Durata Richiesta Overbooking',
        'hours' => 'ore',
        'overbooking_help' => 'Tempo per approvare/rifiutare da parte dell\'admin',
        'cancellation_policy' => 'Politica di Cancellazione',
        'free_cancellation' => 'Cancellazione Gratuita Fino a',
        'late_cancellation_penalty' => 'Penale Cancellazione Tardiva',
        'percent_of_booking' => '% del valore della prenotazione',
        'allow_overbooking' => 'Consenti richieste di overbooking (stato sospeso con approvazione 2h)',
    ],

    // Email Settings
    'email' => [
        'title' => 'Configurazione Email (SMTP)',
        'smtp_host' => 'Host SMTP',
        'smtp_host_placeholder' => 'smtp.esempio.com',
        'smtp_port' => 'Porta SMTP',
        'smtp_username' => 'Username SMTP',
        'smtp_password' => 'Password SMTP',
        'smtp_password_placeholder' => 'Lascia vuoto per mantenere la password attuale',
        'from_name' => 'Nome Mittente',
        'from_email' => 'Email Mittente',
        'admin_notification_email' => 'Email Notifiche Admin',
        'send_test_email' => 'Invia Email di Test',
        'save_smtp' => 'Salva Impostazioni SMTP',
    ],

    // Notification Events
    'notifications' => [
        'title' => 'Eventi di Notifica',
        'event' => 'Evento',
        'admin' => 'Admin',
        'partner' => 'Partner',
        'booking_confirmed' => 'Nuova prenotazione confermata',
        'overbooking_requested' => 'Richiesta overbooking (sospesa)',
        'overbooking_resolved' => 'Overbooking approvato/rifiutato',
        'booking_cancelled' => 'Prenotazione cancellata',
        'booking_modified' => 'Prenotazione modificata',
        'tour_cancelled' => 'Tour cancellato',
        'save_notifications' => 'Salva Impostazioni Notifiche',
    ],

    // Language Settings
    'language' => [
        'title' => 'Impostazioni Lingua',
        'info_message' => 'L\'applicazione supporta multiple lingue. Tutte le etichette, i pulsanti e i messaggi possono essere visualizzati nella lingua selezionata.',
        'default_language' => 'Lingua Predefinita',
        'default_language_help' => 'Lingua predefinita per l\'intera applicazione',
        'partner_language' => 'Lingua Portale Partner',
        'partner_language_help' => 'Lingua per il portale prenotazioni partner',
        'same_as_default' => 'Stessa della predefinita',
        'available_languages' => 'Lingue Disponibili',
        'translated' => '100% tradotto',
        'active' => 'Attiva',
        'available' => 'Disponibile',
        'save_language' => 'Salva Impostazioni Lingua',
    ],

    // Pickup Points
    'pickups' => [
        'title' => 'Punti di Ritiro',
        'add' => 'Aggiungi Punto di Ritiro',
        'name' => 'Nome',
        'location' => 'Posizione',
        'default_time' => 'Orario Predefinito',
        'status' => 'Stato',
        'actions' => 'Azioni',
        'active' => 'Attivo',
        'inactive' => 'Inattivo',
        'no_pickups' => 'Nessun punto di ritiro trovato.',
        'create_one' => 'Creane uno',
    ],

    // Users
    'users' => [
        'title' => 'Utenti Admin',
        'add' => 'Aggiungi Utente',
        'user' => 'Utente',
        'role' => 'Ruolo',
        'last_login' => 'Ultimo Accesso',
        'status' => 'Stato',
        'actions' => 'Azioni',
        'active' => 'Attivo',
        'inactive' => 'Inattivo',
        'never' => 'Mai',
        'edit' => 'Modifica',
        'no_users' => 'Nessun utente trovato.',
        'create_one' => 'Creane uno',
    ],

    // Voucher Settings
    'voucher' => [
        'title' => 'Impostazioni Voucher',
        'info' => 'Personalizza i testi che appaiono sui voucher delle prenotazioni. Questi testi saranno inclusi in tutti i PDF dei voucher.',
        'header_text' => 'Testo Intestazione Voucher',
        'header_text_placeholder' => 'Messaggio di benvenuto o slogan aziendale...',
        'header_text_help' => 'Visualizzato in alto nel voucher, dopo il logo aziendale.',
        'operational_notes' => 'Istruzioni Operative',
        'operational_notes_placeholder' => 'Istruzioni importanti per gli ospiti, punti di incontro, cosa portare...',
        'operational_notes_help' => 'Informazioni importanti visualizzate in evidenza sul voucher. Usa le interruzioni di riga per piu punti.',
        'footer_text' => 'Testo Piede Voucher',
        'footer_text_placeholder' => 'Messaggio di ringraziamento, informazioni di contatto...',
        'footer_text_help' => 'Visualizzato in fondo al voucher.',
    ],

    // Backup & Logs
    'backup' => [
        'title' => 'Backup Database',
        'create_now' => 'Crea Backup Ora',
        'info_message' => 'I backup automatici vengono eseguiti ogni 6 ore (00:00, 06:00, 12:00, 18:00) e vengono conservati per 28 giorni.',
        'recent' => 'Backup Recenti',
        'date' => 'Data',
        'file' => 'File',
        'size' => 'Dimensione',
        'status' => 'Stato',
        'actions' => 'Azioni',
        'success' => 'Completato',
        'failed' => 'Fallito',
        'download' => 'Scarica',
        'no_backups' => 'Nessun backup trovato.',
    ],

    // Logs
    'logs' => [
        'title' => 'Log di Sistema',
        'view_audit' => 'Visualizza Log Audit',
        'view_email' => 'Visualizza Log Email',
        'audit_logs' => 'Log Audit',
        'audit_description' => 'Traccia tutte le modifiche a prenotazioni, tour e partner',
        'email_logs' => 'Log Email',
        'email_description' => 'Visualizza tutte le email inviate e il loro stato di consegna',
        'backup_logs' => 'Log Backup',
        'backup_description' => 'Visualizza la cronologia dei backup e scarica i backup',
        'view' => 'Visualizza',
    ],

    // Buttons
    'save_changes' => 'Salva Modifiche',
];
