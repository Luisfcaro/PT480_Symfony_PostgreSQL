# PT480 Symfony PostgreSQL Project

Welcome to the PT480 Symfony PostgreSQL project. Follow the steps below to set up and run the project on your local machine.

## Prerequisites

- PHP 8.3.*
- Symfony CLI v5.10.*

## E-R Diagram

https://raw.githubusercontent.com/Luisfcaro/PT480_Symfony_PostgreSQL/main/Backend/public/E-R_ÑPT480.png

This proyect has been coded with Symfony 7.1 and PHP 8.2

## 1. Clone the Repository

Start by cloning the repository with the following command:

```bash
git clone https://github.com/Luisfcaro/PT480_Symfony_PostgreSQL.git
```

## 2. Set up the database

### 1. Import the Database Schema:

Locate the pt480.sql file in the project directory. Import this SQL file into your PostgreSQL database. Before doing so, make sure to update the ALTER TABLE ... OWNER TO lines in the SQL file to match your PostgreSQL user.

### 2. Configure the Database Connection

Update the .env file in the Backend directory with your PostgreSQL credentials. Set the DATABASE_URL as follows:

```
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/pt480?serverVersion=16&charset=utf8"
```

Replace user and password with your PostgreSQL username and password, respectively.

## 3 Install Dependencies

Run the following command to install the project dependencies:
```bash
cd Backend/
```

```bash
composer install
```

## 4. Start the Symfony Server

To start the Symfony development server, use:

```bash
symfony server:start
```

## 5. Acces the API Documentation

Once the server is running, you can view the API documentation:

 - Swagger UI: http://localhost:8000/api/doc
 - Swagger JSON: http://localhost:8000/api/doc.json

## 6. Test the API

You can now interact with the API using tools like Postman to ensure everything is set up correctly. Once verified, you can integrate the API with your frontend application.

## Contributing and Issues

If you encounter any issues or have suggestions for improvements, please open an issue or make a contribution to the repository.