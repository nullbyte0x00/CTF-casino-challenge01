USE casino;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    addr VARCHAR(255),
    post_code VARCHAR(255),
    balance INT DEFAULT 0
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    game VARCHAR(50),
    result VARCHAR(50),
    amount INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);



GRANT ALL PRIVILEGES ON *.* TO 'user'@'%';
FLUSH PRIVILEGES;