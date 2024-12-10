-- CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
CREATE USER IF NOT EXISTS gatechUser@localhost IDENTIFIED BY 'gatech123';

DROP DATABASE IF EXISTS `cs6400_fa24_team105`; 
SET default_storage_engine=InnoDB;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS cs6400_fa24_team105 
    DEFAULT CHARACTER SET utf8mb4 
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE cs6400_fa24_team105;

GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `gatechuser`.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `cs6400_fa24_team110`.* TO 'gatechUser'@'localhost';
FLUSH PRIVILEGES;

-- Tables 
CREATE TABLE `Logged_in_User` (
username varchar(50) NOT NULL,
password varchar(100) NOT NULL,
first_name varchar(100) NOT NULL,
last_name varchar(100) NOT NULL,
PRIMARY KEY (username)
);

CREATE TABLE `Inventory_Clerk` (
username varchar(50) NOT NULL,
PRIMARY KEY (username),
FOREIGN KEY (username) REFERENCES `Logged_in_User`(username)
);

CREATE TABLE `Salespeople` (
username varchar(50) NOT NULL,
PRIMARY KEY (username),
FOREIGN KEY (username) REFERENCES `Logged_in_User`(username)
);

CREATE TABLE `Manager` (
username varchar(50) NOT NULL,
PRIMARY KEY (username),
FOREIGN KEY (username) REFERENCES `Logged_in_User`(username)
);

CREATE TABLE `Customer` (
customer_ID varchar(20) NOT NULL,
address_street varchar(100) NOT NULL,
address_city varchar(50) NOT NULL,
address_state varchar(2) NOT NULL,
address_postal varchar(5) NOT NULL,
phone_number varchar(20) NOT NULL,
email_address varchar(250) NULL,
PRIMARY KEY (customer_ID)
);

CREATE TABLE `Individual` (
SSN varchar(20) NOT NULL,
first_name varchar(100) NOT NULL,
last_name varchar(100) NOT NULL,
customer_ID varchar(20) NOT NULL,
PRIMARY KEY (SSN),
FOREIGN KEY (customer_ID) REFERENCES `Customer`(customer_ID)
);

CREATE TABLE `Business` (
ITIN varchar(20) NOT NULL,
primary_contact_first_name varchar(100) NOT NULL,
primary_contact_last_name varchar(100) NOT NULL,
primary_contact_title varchar(100) NOT NULL,
business_name varchar(100) NOT NULL,
customer_ID varchar(20) NOT NULL,
PRIMARY KEY (ITIN),
FOREIGN KEY (customer_ID) REFERENCES `Customer`(customer_ID)
);

CREATE TABLE `Manufacturer_Name` (
manufacturer_name varchar(50) NOT NULL,
PRIMARY KEY (manufacturer_name)
);

CREATE TABLE `Vehicle_Type` (
vehicle_type ENUM('Sedan', 'Coupe', 'Convertible', 'CUV', 'Truck', 'Van', 'Minivan', 'SUV', 'Other') NOT NULL,
PRIMARY KEY (vehicle_type)
);

CREATE TABLE `Vehicle` (
VIN varchar(20) NOT NULL,
model_name varchar(100) NOT NULL,
model_year year NOT NULL,
fuel_type ENUM('Gas', 'Diesel', 'Natural Gas', 'Hybrid', 'Plugin Hybrid', 'Battery', 'Fuel Cell') NOT NULL,
horsepower int unsigned NOT NULL,
description varchar(500) NULL,
sale_price float NULL,
purchase_price float NOT NULL,
vehicle_condition ENUM('Excellent', 'Very Good', 'Good', 'Fair') NOT NULL,
purchase_date date NOT NULL,
seller_customer_ID varchar(20) NOT NULL,
inventoryclerk_username varchar(50) NOT NULL,
vehicle_type ENUM('Sedan', 'Coupe', 'Convertible', 'CUV', 'Truck', 'Van', 'Minivan', 'SUV', 'Other') NOT NULL,
manufacturer_name varchar(50) NOT NULL,
PRIMARY KEY (VIN),
FOREIGN KEY (seller_customer_ID) REFERENCES `Customer`(customer_ID),
FOREIGN KEY (inventoryclerk_username) REFERENCES `Inventory_Clerk`(username),
FOREIGN KEY (vehicle_type) REFERENCES `Vehicle_Type`(vehicle_type),
FOREIGN KEY (manufacturer_name) REFERENCES `Manufacturer_Name`(manufacturer_name)
);

CREATE TABLE `Colors` (
color ENUM(
  'Aluminum', 'Beige', 'Black', 'Blue', 'Brown', 'Bronze', 
  'Claret', 'Copper', 'Cream', 'Gold', 'Gray', 'Green', 
  'Maroon', 'Metallic', 'Navy', 'Orange', 'Pink', 'Purple', 
  'Red', 'Rose', 'Rust', 'Silver', 'Tan', 'Turquoise', 
  'White', 'Yellow') NOT NULL,
PRIMARY KEY (color)
);

CREATE TABLE `Vehicle_color` (
VIN varchar(20) NOT NULL,
color ENUM(
  'Aluminum', 'Beige', 'Black', 'Blue', 'Brown', 'Bronze', 
  'Claret', 'Copper', 'Cream', 'Gold', 'Gray', 'Green', 
  'Maroon', 'Metallic', 'Navy', 'Orange', 'Pink', 'Purple', 
  'Red', 'Rose', 'Rust', 'Silver', 'Tan', 'Turquoise', 
  'White', 'Yellow') NOT NULL,
PRIMARY KEY (VIN, color),
FOREIGN KEY (VIN) REFERENCES `Vehicle`(VIN),
FOREIGN KEY (color) REFERENCES `Colors`(color)
);

CREATE TABLE `Buy` (
VIN varchar(20) NOT NULL,
buyer_customer_ID varchar(20) NOT NULL,
salespeople_username varchar(50) NOT NULL,
sale_date date NOT NULL,
PRIMARY KEY (VIN, buyer_customer_ID, salespeople_username),
FOREIGN KEY (VIN) REFERENCES `Vehicle`(VIN),
FOREIGN KEY (buyer_customer_ID) REFERENCES `Customer`(customer_ID),
FOREIGN KEY (salespeople_username) REFERENCES `Salespeople`(username)
);

CREATE TABLE `Vendor` (
name varchar(50) NOT NULL,
address_street varchar(100) NOT NULL,
address_city varchar(50) NOT NULL,
address_state varchar(2) NOT NULL,
address_postal varchar(5) NOT NULL,
phone_number varchar(24) NOT NULL,
PRIMARY KEY (name)
);

CREATE TABLE `Parts_Order` (
VIN varchar(20) NOT NULL,  -- Incremental order number per VIN
num int unsigned NOT NULL, -- Define order_number based on requirements
order_number varchar(24) GENERATED ALWAYS AS (CONCAT(VIN, '-', LPAD(num, 3, '0'))) STORED,
total_cost float NOT NULL DEFAULT 0,
vendor_name varchar(50) NOT NULL,
PRIMARY KEY (VIN, order_number),  
FOREIGN KEY (VIN) REFERENCES `Vehicle`(VIN),
FOREIGN KEY (vendor_name) REFERENCES `Vendor`(name)
);

CREATE TABLE `Determine_Parts_Order` (
VIN varchar(20) NOT NULL,
order_number varchar(24) NOT NULL,
inventoryclerk_username varchar(50) NOT NULL,
PRIMARY KEY (VIN, order_number, inventoryclerk_username),  
-- FOREIGN KEY (VIN) REFERENCES `Vehicle`(VIN),
FOREIGN KEY (VIN, order_number) REFERENCES `Parts_Order`(VIN, order_number),
FOREIGN KEY (inventoryclerk_username) REFERENCES `Inventory_Clerk`(username)
);

CREATE TABLE `Part` (
VIN varchar(20) NOT NULL,
order_number varchar(24)  NOT NULL,
vendor_part_number varchar(50) NOT NULL,
status ENUM('ordered', 'received', 'installed') NOT NULL,
description varchar(500) NOT NULL,
unit_price float NOT NULL,
quantity int unsigned NOT NULL,
PRIMARY KEY (VIN, order_number, vendor_part_number),  
-- FOREIGN KEY (VIN) REFERENCES `Vehicle`(VIN),
FOREIGN KEY (VIN, order_number) REFERENCES `Parts_Order`(VIN, order_number)
);

