# Local configuration for VaxiCare

`config.php` reads settings from process environment variables. `.env.example`
documents the variable names, but PHP does not load `.env` files by itself.

For XAMPP on Windows, add the required variables to Apache's environment (for
example with `SetEnv VAXICARE_DB_NAME vaccine_registration` in the Apache
configuration) or define them in Windows before starting Apache. Restart Apache
after changing its environment. Keep SMTP and deployment database passwords
outside the project directory.

The local XAMPP defaults remain `localhost`, `root`, an empty MySQL password,
and database `vaccine_registration`. For Gmail OTP delivery, set
`VAXICARE_SMTP_USERNAME`, `VAXICARE_SMTP_PASSWORD` (a Google App Password),
and optionally `VAXICARE_SMTP_FROM_EMAIL` and `VAXICARE_SMTP_FROM_NAME`.
Without SMTP settings, OTP email sending fails safely and logs a configuration
message; registration pages and the rest of the application remain available.
Set `VAXICARE_ADMIN_USERNAME` and `VAXICARE_ADMIN_PASSWORD` before the first
application request to create the initial admin account. Choose a unique
password and remove these bootstrap variables after the account is created.

Do not commit `.env`, local configuration, logs, database backups, or real user
uploads. The seed accounts are fictional dashboard data and cannot be used to
log in.
