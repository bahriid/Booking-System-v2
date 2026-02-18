<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MagShip Notification')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f8fa;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-content {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background-color: #009ef7;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-header .subtitle {
            margin-top: 5px;
            font-size: 14px;
            opacity: 0.9;
        }

        .email-body {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #181c32;
        }

        .message {
            font-size: 15px;
            color: #5e6278;
            margin-bottom: 25px;
        }

        .info-box {
            background-color: #f5f8fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-box-header {
            font-size: 14px;
            font-weight: 600;
            color: #181c32;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px solid #e4e6ef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            display: table-cell;
            color: #7e8299;
            font-size: 14px;
            width: 40%;
            padding-right: 10px;
        }

        .info-value {
            display: table-cell;
            color: #181c32;
            font-weight: 600;
            font-size: 14px;
        }

        .booking-code {
            font-family: monospace;
            font-size: 20px;
            font-weight: bold;
            color: #009ef7;
            background-color: #f1faff;
            padding: 15px 25px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 25px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmed {
            background-color: #e8fff3;
            color: #50cd89;
        }

        .status-pending {
            background-color: #fff8dd;
            color: #ffc700;
        }

        .status-cancelled {
            background-color: #fff5f8;
            color: #f1416c;
        }

        .status-rejected {
            background-color: #fff5f8;
            color: #f1416c;
        }

        .status-expired {
            background-color: #f8f5ff;
            color: #7239ea;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
        }

        .btn-primary {
            background-color: #009ef7;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0095e8;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .alert-warning {
            background-color: #fff8dd;
            border-left: 4px solid #ffc700;
            color: #7e6115;
        }

        .alert-danger {
            background-color: #fff5f8;
            border-left: 4px solid #f1416c;
            color: #8c3550;
        }

        .alert-success {
            background-color: #e8fff3;
            border-left: 4px solid #50cd89;
            color: #1e7d53;
        }

        .alert-info {
            background-color: #f1faff;
            border-left: 4px solid #009ef7;
            color: #0067a5;
        }

        .passengers-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .passengers-table th {
            background-color: #f5f8fa;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #7e8299;
            text-transform: uppercase;
            border-bottom: 1px solid #e4e6ef;
        }

        .passengers-table td {
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #f5f8fa;
        }

        .pax-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .pax-adult {
            background-color: #f1faff;
            color: #009ef7;
        }

        .pax-child {
            background-color: #fff8dd;
            color: #f5a623;
        }

        .pax-infant {
            background-color: #f5f5f5;
            color: #7e8299;
        }

        .email-footer {
            padding: 25px 30px;
            background-color: #f5f8fa;
            text-align: center;
            font-size: 13px;
            color: #7e8299;
        }

        .email-footer a {
            color: #009ef7;
            text-decoration: none;
        }

        .divider {
            border-top: 1px solid #e4e6ef;
            margin: 25px 0;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px;
            }

            .email-body {
                padding: 20px;
            }

            .info-row {
                display: block;
            }

            .info-label,
            .info-value {
                display: block;
                width: 100%;
            }

            .info-label {
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            @yield('content')

            <div class="email-footer">
                <p>This is an automated message from MagShip B2B Booking.</p>
                <p>Please do not reply directly to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
