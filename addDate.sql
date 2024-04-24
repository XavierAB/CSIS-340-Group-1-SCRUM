-- Add 5 users to the users table
INSERT INTO users (firstname, lastname, username, password, email)
VALUES
    ('John', 'Doe', 'johndoe', 'password1', 'john@example.com'),
    ('Jane', 'Smith', 'janesmith', 'password2', 'jane@example.com'),
    ('Alice', 'Johnson', 'alicej', 'password3', 'alice@example.com'),
    ('Bob', 'Williams', 'bobw', 'password4', 'bob@example.com'),
    ('Emily', 'Brown', 'emilyb', 'password5', 'emily@example.com');

-- Add 10 electronic items to the items table
INSERT INTO items (name, stock, price)
VALUES
    ('Laptop', 20, 999.99),
    ('Smartphone', 30, 699.99),
    ('Tablet', 25, 399.99),
    ('Smartwatch', 50, 199.99),
    ('Headphones', 40, 99.99),
    ('Camera', 15, 899.99),
    ('Wireless Earbuds', 35, 149.99),
    ('Gaming Console', 10, 499.99),
    ('Bluetooth Speaker', 20, 79.99),
    ('External Hard Drive', 30, 129.99);
