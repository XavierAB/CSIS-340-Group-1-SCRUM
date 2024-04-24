CREATE TABLE info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    cost DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (item_id) REFERENCES items(id)
);