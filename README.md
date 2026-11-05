# VaxiCare — Online Vaccine Registration System

VaxiCare is a web-based application that helps users register for vaccination, book available vaccine slots, and access vaccination certificates. Administrators can manage registrations, vaccination slots, and vaccine stock.

## Features

### User
- Register and log in
- Book available vaccination slots
- View bookings and vaccination certificates
- Manage profile information

### Admin
- Manage user registrations
- Manage vaccination slots and vaccine stock
- View bookings and issue certificates

## Technologies Used

- PHP
- MySQL
- HTML, CSS, JavaScript
- Bootstrap

## Run Locally

1. Install **XAMPP** and start Apache and MySQL.
2. Copy the project folder into XAMPP's `htdocs` directory.
3. Open phpMyAdmin and create a database for the project.
4. Import the project's SQL database file into that database.
5. Configure the database connection using `config.example.php` as a reference. Keep your actual credentials in your local configuration and do not upload passwords or secrets.
6. Open the project in your browser through `http://localhost/<project-folder>/`.

> Update the database name and project-folder path to match your local setup.

## Security Note

This repository is intended for learning and demonstration. Use your own local configuration and never commit real passwords, email app passwords, API keys, or personal information.

## License

No license has been specified yet.
