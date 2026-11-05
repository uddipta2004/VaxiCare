<?php
// Safe reference for deployment environment variables. config.php reads these
// variables directly; this example intentionally contains placeholders only.
return [
    'VAXICARE_DB_HOST' => 'localhost',
    'VAXICARE_DB_USERNAME' => 'your_database_user',
    'VAXICARE_DB_PASSWORD' => 'replace_with_local_secret',
    'VAXICARE_DB_NAME' => 'vaccine_registration',
    'VAXICARE_ADMIN_USERNAME' => 'your_admin_username',
    'VAXICARE_ADMIN_PASSWORD' => 'choose_a_unique_admin_password',
    'VAXICARE_SMTP_USERNAME' => 'your-address@gmail.com',
    'VAXICARE_SMTP_PASSWORD' => 'your-google-app-password',
    'VAXICARE_SMTP_FROM_EMAIL' => 'your-address@gmail.com',
    'VAXICARE_SMTP_FROM_NAME' => 'VaxiCare',
];
