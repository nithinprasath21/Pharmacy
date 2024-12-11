# Pharmacy Management System

## Introduction
This project is a web application for managing a pharmacy, allowing users to handle inventory, customer billing, and transaction records. It is built using PHP and MongoDB, providing a user-friendly interface for pharmacy management.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Setting Up MongoDB](#setting-up-mongodb)
- [Installing the MongoDB Driver for PHP](#installing-the-mongodb-driver-for-php)
- [Running the Application](#running-the-application)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Prerequisites
Before you begin, ensure you have met the following requirements:
- XAMPP installed on your machine.
- Composer installed globally (for managing PHP dependencies).
- Basic knowledge of PHP and web development.

## Installation
1. **Download XAMPP**:  
   Download XAMPP from [Apache Friends](https://www.apachefriends.org/index.html) and install it on your system.

2. **Clone the Repository**:  
   ```bash
   git clone https://github.com/nithinprasath21/Pharmacy.git
   cd Pharmacy
Move the Project Folder:
Move the cloned project folder to the htdocs directory of your XAMPP installation (e.g., C:\xampp\htdocs).
Setting Up MongoDB
Install MongoDB:
Download MongoDB from the official website: MongoDB Download Center.
Follow the installation instructions for your operating system.

Start MongoDB:
Open a command prompt and run:

bash
Copy code
mongod
This will start the MongoDB server.

Installing the MongoDB Driver for PHP
Open Command Prompt:
Navigate to your PHP installation directory within XAMPP (e.g., C:\xampp\php).

Use Composer to Install the Driver:

bash
Copy code
composer require mongodb/mongodb
Enable MongoDB Extension in PHP:

Open php.ini located in C:\xampp\php\php.ini.
Uncomment or add the following line:
text
Copy code
extension=mongodb.so  ; For Linux or macOS
extension=php_mongodb.dll ; For Windows
Save the changes and restart Apache from the XAMPP control panel.
Running the Application
Start Apache and MongoDB:

Open XAMPP Control Panel.
Start both Apache and MySQL services.
Access the Application:
Open your web browser and go to:
http://localhost/Pharmacy

Usage
Once you have set up everything, you can navigate through different features of the application including managing inventory, processing customer billing, and viewing transaction history.

Contributing
Contributions are welcome! Please fork this repository and submit a pull request for any improvements or features.

License
This project is licensed under the MIT License.


### Key Notes:
- All text is properly formatted using Markdown.
- Links are clickable and commands are shown in code blocks.
- Sections are clearly structured for ease of understanding and navigation.

This `README.md` file ensures users can easily set up, run, and contribute to your project. Let me know if you need further modifications!
