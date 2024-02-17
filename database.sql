lCREATE TABLE Rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(255) NOT NULL
);


CREATE TABLE Displays (
    display_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    display_name VARCHAR(255),
    ip_address VARCHAR(255),
    status ENUM('Active', 'Inactive') NOT NULL,
    FOREIGN KEY (room_id) REFERENCES Rooms(room_id)
);


CREATE TABLE `deviceUsage` (
  `ID` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `displayID` int(10) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `playtime` TIME DEFAULT NULL, 
  `InTime` timestamp NULL DEFAULT current_timestamp(),
  `OutTime` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `Fees` varchar(120) DEFAULT NULL,
  `status` ENUM('available', 'unavailable') NOT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `UpdateTime` TIME DEFAULT CURRENT_TIME(), 
  `cafeItemName` VARCHAR(255) DEFAULT NULL,
  `cafeItemPrice` DECIMAL(10,2) DEFAULT NULL,
  FOREIGN KEY (`displayID`) REFERENCES `Displays`(`display_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DELIMITER $$
CREATE TRIGGER updateUpdateTime BEFORE UPDATE ON deviceUsage
FOR EACH ROW
BEGIN
    SET NEW.UpdateTime = CURRENT_TIME();
END$$
DELIMITER ;



CREATE TABLE `historyUsage` (
    ID int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    displayID int(10) DEFAULT NULL,
    name varchar(120) DEFAULT NULL,
    MobileNumber bigint(10) DEFAULT NULL,
    playtime TIME DEFAULT NULL,
    InTime timestamp NULL DEFAULT current_timestamp(),
    OutTime timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
    Fees varchar(120) DEFAULT NULL,
    UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
    date DATE DEFAULT CURDATE(), 
    `cafeItemName` VARCHAR(255) DEFAULT NULL,
    `cafeItemPrice` DECIMAL(10,2) DEFAULT NULL,
    FOREIGN KEY (displayID) REFERENCES Displays(display_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE CafeItems (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50),
    available BOOLEAN NOT NULL DEFAULT TRUE,
    quantity INT DEFAULT 0
);
