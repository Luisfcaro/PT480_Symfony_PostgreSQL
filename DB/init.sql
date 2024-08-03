-- Create the database
CREATE DATABASE PT480;

-- Create the Users table
CREATE TABLE Users (
    Id SERIAL PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Surname VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL
);

-- Create the Sensors table
CREATE TABLE Sensors (
    Id SERIAL PRIMARY KEY,
    Name VARCHAR(255) NOT NULL
);

-- Create the Wines table
CREATE TABLE Wines (
    Id SERIAL PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Year INT NOT NULL
);

-- Create the Measurements table
CREATE TABLE Measurements (
    Year INT NOT NULL,
    Sensor_id INT NOT NULL,
    Wine_id INT NOT NULL,
    Colour VARCHAR(255),
    Temperature FLOAT,
    Graduation FLOAT,
    Ph FLOAT,
    PRIMARY KEY (Year, Sensor_id, Wine_id),
    FOREIGN KEY (Sensor_id) REFERENCES Sensors(Id),
    FOREIGN KEY (Wine_id) REFERENCES Wines(Id)
);
