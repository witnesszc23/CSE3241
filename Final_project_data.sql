-- CREATE DATABASE finalproject;
USE finalproject;

CREATE TABLE IF NOT EXISTS stock_data (
    stock_label VARCHAR(10),
    trading_date DATE,
    stock_price DECIMAL(10, 2)
);

-- LOAD DATA INFILE '/tmp/AMZN.csv'
LOAD DATA INFILE 'D:/Git/CSE3241/AMZN.csv'
INTO TABLE stock_data
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"' 
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(@Date, @Price, @ignore1, @ignore2, @ignore3, @ignore4, @ignore5)
SET stock_label = 'AMZN', 
    trading_date = STR_TO_DATE(@Date, '%m/%d/%Y'),
    stock_price = @Price;
    
-- LOAD DATA INFILE '/tmp/AAPL.csv'
LOAD DATA INFILE 'D:/Git/CSE3241/AAPL.csv'
INTO TABLE stock_data
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"' 
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(@Date, @Price, @ignore1, @ignore2, @ignore3, @ignore4, @ignore5)
SET stock_label = 'AAPL', 
    trading_date = STR_TO_DATE(@Date, '%m/%d/%Y'),
    stock_price = @Price;

-- LOAD DATA INFILE '/tmp/GOOGL.csv'
LOAD DATA INFILE 'D:/Git/CSE3241/GOOGL.csv'
INTO TABLE stock_data
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"' 
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(@Date, @Price, @ignore1, @ignore2, @ignore3, @ignore4, @ignore5)
SET stock_label = 'GOOGL', 
    trading_date = STR_TO_DATE(@Date, '%m/%d/%Y'),
    stock_price = @Price;

-- LOAD DATA INFILE '/tmp/META.csv'
LOAD DATA INFILE 'D:/Git/CSE3241/META.csv'
INTO TABLE stock_data
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"' 
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(@Date, @Price, @ignore1, @ignore2, @ignore3, @ignore4, @ignore5)
SET stock_label = 'META', 
    trading_date = STR_TO_DATE(@Date, '%m/%d/%Y'),
    stock_price = @Price;
