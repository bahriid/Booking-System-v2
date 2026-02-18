<?php

return [
    // Page titles
    'dashboard' => 'Dashboard',
    'my_bookings' => 'My Bookings',
    'new_booking' => 'New Booking',
    'booking_details' => 'Booking Details',
    'edit_booking' => 'Edit Booking',

    // Breadcrumbs
    'bookings' => 'Bookings',
    'edit' => 'Edit',

    // Dashboard - Welcome
    'welcome' => 'Welcome, :name!',
    'welcome_message' => 'From here you can manage your bookings and create new booking requests for your guests.',

    // Dashboard - Stats
    'bookings_this_month' => 'Bookings This Month',
    'total_passengers' => 'Total Passengers',
    'pending_requests' => 'Pending Requests',

    // Dashboard - Quick Actions
    'quick_actions' => 'Quick Actions',
    'view_all_bookings' => 'View All Bookings',

    // Dashboard - Recent Bookings
    'recent_bookings' => 'Recent Bookings',
    'your_latest_booking_requests' => 'Your latest booking requests',
    'view_all' => 'View All',
    'no_bookings_yet' => 'No bookings yet',
    'create_first_booking' => 'Create Your First Booking',

    // Dashboard - Upcoming Tours
    'your_upcoming_tours' => 'Your Upcoming Tours',
    'tours_next_7_days' => 'Tours scheduled for the next 7 days',
    'today' => 'Today',
    'tomorrow' => 'Tomorrow',
    'details' => 'Details',
    'create_new_booking' => 'Create new booking',
    'download_voucher' => 'Download Voucher',

    // Table headers
    'code' => 'Code',
    'booking_code' => 'Booking Code',
    'tour' => 'Tour',
    'date' => 'Date',
    'time' => 'Time',
    'pax' => 'Pax',
    'status' => 'Status',
    'created' => 'Created',
    'actions' => 'Actions',
    'name' => 'Name',
    'type' => 'Type',
    'pickup_point' => 'Pickup Point',
    'phone' => 'Phone',
    'allergies' => 'Allergies',
    'notes' => 'Notes',

    // Time status
    'min_left' => ':minutes min left',
    'expired' => 'Expired',

    // Filters
    'search_booking_code' => 'Search booking code...',
    'all_status' => 'All Status',
    'all_tours' => 'All Tours',
    'clear_filters' => 'Clear filters',
    'no_bookings_found' => 'No bookings found.',

    // Booking wizard - Step titles
    'select_tour' => 'Select Tour',
    'choose_your_tour' => 'Choose your tour',
    'date_passengers' => 'Date & Passengers',
    'select_date_and_count' => 'Select date and count',
    'passenger_details' => 'Passenger Details',
    'enter_passenger_info' => 'Enter passenger info',
    'review_submit' => 'Review & Submit',
    'confirm_booking' => 'Confirm booking',

    // Booking wizard - Step 1
    'choose_tour_to_book' => 'Choose the tour you want to book.',
    'no_tours_available' => 'No tours are currently available. Please try again later.',
    'pax_max' => ':count pax max',

    // Booking wizard - Step 2
    'select_departure_date' => 'Select departure date, time, and number of passengers.',
    'tour_date' => 'Tour Date',
    'select_date' => 'Select date',
    'available_time_slots' => 'Available Time Slots',
    'no_departures_available' => 'No departures available for this date. Please select another date.',
    'adults' => 'Adults',
    'children' => 'Children',
    'infants' => 'Infants',
    'years_12_plus' => '12+ years',
    'years_2_11' => '2-11 years',
    'years_0_1' => '0-1 years (free)',
    'available_seats' => 'Available Seats',
    'selected_passengers' => 'Selected passengers',
    'seats' => ':count seats',
    'full' => 'Full',
    'cutoff' => 'Cut-off',

    // Booking wizard - Step 2 - Overbooking
    'overbooking_request' => 'Overbooking Request',
    'overbooking_warning' => 'This booking exceeds available capacity and will require admin approval.',

    // Booking wizard - Step 3
    'enter_details_each_passenger' => 'Enter details for each passenger.',
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'select_pickup_point' => 'Select pickup point...',

    // Booking wizard - Step 4
    'review_before_submit' => 'Please review your booking details before submitting.',
    'date_time' => 'Date & Time',
    'passengers' => 'Passengers',
    'pending_approval' => 'Pending Approval',
    'will_be_confirmed' => 'Will be Confirmed',
    'overbooking_notice' => 'This booking will be submitted for admin approval. You have 2 hours for approval before it expires.',
    'adult' => 'Adult',
    'child' => 'Child',
    'infant' => 'Infant',

    // Booking wizard - Buttons
    'back' => 'Back',
    'continue' => 'Continue',
    'submit_booking' => 'Submit Booking',
    'please_wait' => 'Please wait...',

    // Booking wizard - Validation
    'please_select_tour' => 'Please select a tour.',
    'please_select_date_time' => 'Please select a date and time slot.',
    'at_least_one_adult' => 'At least one adult is required.',
    'fill_passenger_details' => 'Please fill in all required passenger details (first name, last name, and pickup point).',
    'error_loading_departures' => 'Error loading departures. Please try again.',

    // Show booking
    'booking_summary' => 'Booking Summary',
    'tour_information' => 'Tour Information',
    'pickup_information' => 'Pickup Information',
    'guests_should_be_at' => 'Your guests should be at',
    'by' => 'by',
    'booking_confirmed' => 'Your booking is confirmed',
    'awaiting_approval' => 'Awaiting admin approval',
    'booking_no_longer_active' => 'Booking is no longer active',
    'last_updated' => 'Last Updated',

    // Cancellation
    'cancellation_policy' => 'Cancellation Policy',
    'free_cancellation' => 'Free Cancellation',
    'until' => 'Until :datetime',
    'cancellation_penalty_applies' => 'Cancellation Penalty Applies',
    'no_show_penalty' => '100% no-show penalty will be charged',
    'no_show_penalty_account' => 'A 100% no-show penalty will be charged to your account.',
    'policy' => 'Policy',
    'policy_description' => 'Free cancellation up to 48h before departure. Cancellation within 24h = 100% no-show penalty.',
    'request_cancellation' => 'Request Cancellation',
    'free_cancellation_available' => 'Free Cancellation Available',
    'cancel_without_penalty' => 'This booking can be cancelled without penalty.',
    'cancellation_reason' => 'Cancellation Reason (optional)',
    'cancellation_reason_placeholder' => 'Please provide a reason for cancellation...',
    'close' => 'Close',
    'confirm_cancellation' => 'Confirm Cancellation',

    // Actions
    'edit' => 'Edit',
    'view' => 'View',
    'edit_booking' => 'Edit Booking',
    'download_voucher_pdf' => 'Download Voucher (PDF)',
    'preview_voucher' => 'Preview Voucher',
    'back_to_bookings' => 'Back to Bookings',

    // Edit booking
    'booking_information' => 'Booking Information',
    'editable' => 'Editable',
    'modify_passenger_details' => 'You can modify passenger details and pickup points',
    'cannot_change_tour_date' => 'The tour, date, and time cannot be changed. To change these, cancel this booking and create a new one.',
    'booking_notes' => 'Booking Notes',
    'booking_notes_placeholder' => 'Add any special notes for this booking...',
    'passenger' => 'Passenger :number',
    'select_pickup' => '-- Select Pickup Point --',
    'any_food_allergies' => 'Any food allergies...',
    'special_requirements' => 'Special requirements...',
    'total' => 'total',
    'summary' => 'Summary',
    'total_amount' => 'Total Amount',
    'save_changes' => 'Save Changes',
    'cancel' => 'Cancel',
    'add_passenger' => 'Add Passenger',
    'remove_passenger' => 'Remove Passenger',
    'confirm_remove_passenger' => 'Are you sure you want to remove this passenger',
    'cannot_remove_last_passenger' => 'You cannot remove the last passenger. Cancel the booking instead.',
    'no_seats_available' => 'No more seats available for this departure.',
    'new' => 'New',
    'capacity' => 'Capacity',
];
