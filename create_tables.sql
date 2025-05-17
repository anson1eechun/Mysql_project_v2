USE D1371716;

-- 建立獎項資料表
CREATE TABLE award (
    id INT PRIMARY KEY AUTO_INCREMENT,
    award_ID VARCHAR(50),
    sort VARCHAR(50),
    title VARCHAR(100),
    organizer VARCHAR(50),
    date VARCHAR(50),
    topic VARCHAR(100),
    student_list VARCHAR(100),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立會議資料表
CREATE TABLE conference (
    conf_ID VARCHAR(50) PRIMARY KEY,
    conf_character VARCHAR(100),
    title VARCHAR(100),
    name VARCHAR(100),
    pages INT(50),
    date VARCHAR(50),
    location VARCHAR(50),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立課程資料表
CREATE TABLE courses (
    courses_ID VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) COMMENT '課程名稱',
    time VARCHAR(100) COMMENT '上課時間',
    location VARCHAR(50) COMMENT '上課地點',
    class_name VARCHAR(50) COMMENT '授課班級',
    pro_ID VARCHAR(50),
    FOREIGN KEY (pro_ID) REFERENCES professor(pro_ID)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立學歷資料表
CREATE TABLE education (
    edu_ID VARCHAR(50) PRIMARY KEY,
    department VARCHAR(100),
    degree VARCHAR(100),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立經歷資料表
CREATE TABLE experience (
    experience_ID VARCHAR(50) PRIMARY KEY,
    sort VARCHAR(50),
    department VARCHAR(100),
    position VARCHAR(100),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立專長資料表
CREATE TABLE expertise (
    expertise_ID VARCHAR(50) PRIMARY KEY,
    item VARCHAR(100),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立期刊資料表
CREATE TABLE journal (
    jour_ID VARCHAR(50) PRIMARY KEY,
    jour_character VARCHAR(100),
    title VARCHAR(100),
    name VARCHAR(100),
    issue VARCHAR(50),
    date VARCHAR(50),
    pages INT(50),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立演講資料表
CREATE TABLE lecture (
    lecture_ID VARCHAR(50) PRIMARY KEY,
    title VARCHAR(100),
    location VARCHAR(50),
    date VARCHAR(50),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 建立教授資料表
CREATE TABLE IF NOT EXISTS professor (
    pro_ID VARCHAR(10) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    introduction TEXT,
    photo VARCHAR(255),
    email VARCHAR(100),
    extension VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 建立計畫資料表
CREATE TABLE project (
    project_ID VARCHAR(50) PRIMARY KEY,
    sort VARCHAR(50),
    name VARCHAR(100),
    date VARCHAR(50),
    number VARCHAR(50),
    role VARCHAR(50),
    pro_ID VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
