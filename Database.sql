CREATE TABLE IF NOT EXISTS Permission 
( 
Permission_Id int AUTO_INCREMENT, 
Name varchar(30), 
Description varchar(50), 
PRIMARY KEY (Permission_Id) 
);
CREATE TABLE IF NOT EXISTS Role 
( Role_Id int AUTO_INCREMENT, 
Name varchar(30), 
Description varchar(50), 
PRIMARY KEY (Role_Id) 
)
CREATE TABLE IF NOT EXISTS DPBR 
( Role_Id int, 
Permission_Id int, 
INDEX FK_DBPR_Role (Role_Id), 
INDEX FK_DBRP_Permission (Permission_Id), 
FOREIGN KEY (Role_Id) REFERENCES Role (Role_Id), 
FOREIGN KEY (Permission_Id) 
REFERENCES Permission (Permission_Id) 
);
CREATE TABLE IF NOT EXISTS User 
( 
User_Id int AUTO_INCREMENT, 
UserAuth varchar(60), 
Password varchar(255), 
Role_Id int, 
ConfirmEmail boolean DEFAULT NULL,
INDEX FK_User_Role (Role_Id), 
PRIMARY KEY (User_Id), 
FOREIGN KEY (Role_Id) REFERENCES Role (Role_Id) ON DELETE CASCADE 
);
CREATE TABLE IF NOT EXISTS AccountNotIdentified
(
User_Id int,
Token varchar(20),
INDEX FK_ANI_User (User_Id),
FOREIGN KEY (User_Id) REFERENCES User (User_Id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS tokenuser 
( 
User_Id int, 
Token varchar(255), 
INDEX FK_token_user (User_Id), 
FOREIGN KEY (User_Id) REFERENCES user (User_Id) 
);
