-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3306
-- 產生時間： 2025 年 06 月 12 日 23:42
-- 伺服器版本： 10.11.11-MariaDB-0ubuntu0.24.04.2
-- PHP 版本： 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `D1371716`
--

-- --------------------------------------------------------

--
-- 資料表結構 `administrator`
--

CREATE TABLE `administrator` (
  `username` varchar(100) NOT NULL,
  `password_hash` char(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `administrator`
--

INSERT INTO `administrator` (`username`, `password_hash`, `created_at`) VALUES
('anson', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2025-06-09 23:23:34'),
('D1260175', '63ecaf0f4ca314962b97f1f2e5a0c3d2794e973b1b9a36180925b476aa20effc', '2025-06-11 17:22:00'),
('D1371511', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2025-06-10 02:42:02'),
('kuo', 'c7e616822f366fb1b5e0756af498cc11d2c0862edcb32ca65882f622ff39de1b', '2025-06-13 01:00:47'),
('valerie', '2ac9a6746aca543af8dff39894cfe8173afba21eb01c6fae33d52947222855ef', '2025-06-10 23:59:44');

-- --------------------------------------------------------

--
-- 資料表結構 `award`
--

CREATE TABLE `award` (
  `award_ID` varchar(50) NOT NULL COMMENT '獎項ID',
  `type` varchar(50) DEFAULT NULL COMMENT '獎項類別',
  `title` varchar(200) DEFAULT NULL COMMENT '獎項名稱',
  `organizer` varchar(100) DEFAULT NULL COMMENT '主辦單位',
  `date` varchar(50) DEFAULT NULL COMMENT '日期',
  `topic` varchar(200) DEFAULT NULL COMMENT '參賽主題',
  `student_list` varchar(200) DEFAULT NULL COMMENT '指導學生名單',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `award`
--

INSERT INTO `award` (`award_ID`, `type`, `title`, `organizer`, `date`, `topic`, `student_list`, `pro_ID`) VALUES
('H001', '校內獎勵', 'PUGB', 'apex', '2006-11-11', '', '', 'A001'),
('I001', '校外獎勵', 'Best Paper Award', 'IEEE ECICE 2023', '2023-11-10', NULL, NULL, 'A001'),
('I002', '指導學生獲獎', '2023 全國私立大專校院誠是競賽', '銘傳大學', '2023-06-29', NULL, '指導學生參賽(Chia-Hui Chen, Kai-Xiang Chang, Ting-Feng Ho)', 'A001'),
('I003', '指導學生獲獎', '2022 永續智慧創新黑客松競賽', '靜宜大學', '2022-12-11', NULL, '指導學生參賽(黃品慈、徐筱婷、許恩翔、 陳基竹、林育萱、李明燁、郭柏陞)', 'A001');

-- --------------------------------------------------------

--
-- 資料表結構 `conference`
--

CREATE TABLE `conference` (
  `conf_ID` varchar(50) NOT NULL COMMENT '會議ID',
  `conf_character` varchar(100) DEFAULT NULL COMMENT '作者',
  `title` varchar(200) DEFAULT NULL COMMENT '論文標題',
  `name` varchar(200) DEFAULT NULL COMMENT '會議名稱',
  `pages` varchar(50) DEFAULT NULL COMMENT '頁數',
  `date` varchar(50) DEFAULT NULL COMMENT '日期',
  `location` varchar(50) DEFAULT NULL COMMENT '地點',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `conference`
--

INSERT INTO `conference` (`conf_ID`, `conf_character`, `title`, `name`, `pages`, `date`, `location`, `pro_ID`) VALUES
('G001', 'Tang-Wei Su, Jhe-Wei Lin*, Cheng-Hsuan Lee, Wei-Ming Tseng', 'Importing Diffusion and Neural Styles for Realistic Face Generation', 'IEEE Eurasia Conference on IoT, Communication and Engineering', NULL, '2023-10', 'National Formosa University', 'A001'),
('G002', 'Jhe-Wei Lin, Tun-Yen Nien', 'Evaluate the Chinese Story Cycle Generation Based on part-of-speech Matching and Contextual Coherence', 'International Conferenece on Applied System Innovation', NULL, '2022-04', 'FULI HOT SPRING RESORT', 'A001'),
('G003', 'Jhe-Wei Lin, Van-Tam Hoang, Ting-Hsuan Chien, Rong-Guey Chang, I-Ling Kuo', 'Nutritionist based on Deep Learning', 'IEEE International Conference on Applied System Innovation', 'pp. 49-53', '2021-09', 'Alishan House', 'A001'),
('G004', 'Cheng-Yan Siao, Jhe-Wei Lin and Rong-Guey Chang', 'A Fast Method to Detect Collision for Five-axis Machining with GPU', '2020 IEEE Eurasia Conference on IOT, Communication and Engineering', 'pp. 366-369', '2020-10', 'Yunlin', 'A001'),
('G005', 'Cheng-Yan Siao, Jhe-Wei Lin, Rong-Guey Chang', 'Robot Language Compiler', '2020 IEEE Eurasia Conference on IOT, Communication and Engineering', 'pp. 399-402', '2020-10', 'Yunlin', 'A001'),
('G006', 'Cheng-Yan Siao*, Jhe-Wei Lin and Rong-Guey Chang', 'The Design and Implementation of A Delivery System', '2020 IEEE Eurasia Conference on IOT, Communication and Engineering', 'pp. 160-163', '2020-10', 'Yunlin', 'A001'),
('G007', 'Jhe-Wei Lin, Cheng-Yan Siao, Chia-Hsuan Lin and Rong-Guey Chang', 'Collision Detection of Industrial Automation', '2020 IEEE International Conference on Mechatronics, Robotics and Automation', 'pp. 43-47', '2020-10', 'Shanghai', 'A001'),
('G008', 'Jhe-Wei Lin, Jo-Han Tseng and Rong-Guey Chang', 'Chinese Story Generation Using Conditional Generative Adversarial Network', '2020 IEEE International Conference on Artificial Intelligence in Information and Communication', 'pp. 457-462', '2020-02', 'Takakura Hotel Fukuoka', 'A001'),
('G009', 'Jhe-Wei Lin, Yu-Che Gao and Rong-Guey Chang', 'Chinese Story Generation with FastText Transformer Network', '2019 IEEE International Conference on Artificial Intelligence in Information and Communication', 'pp. 395-398', '2019-02', 'Pacific Hotel Okinawa', 'A001'),
('G010', 'Ting-Hsuan Chien, Jhe-Wei Lin and Rong-Guey Chang', 'Parallel Collision Detection with OpenMP', '2018 International Conference on Information System and Artificial Intelligence', NULL, '2018-06', 'Grand Trustel Aster Suzhou', 'A001'),
('G011', 'Ting-Hsuan Chien, Yu-Hsin Lu, Jhe-Wei Lin and Rong-Guey Chang', 'An Enhanced Depth Estimation System Using RGB-D Cameras and Gyroscopes', '2017 IEEE International Conference on Applied System Innovation', 'pp. 1554-155', '2017-05', 'Hotel emisia, Sapporo', 'A001'),
('G012', '綠谷出久', '高歌離席', 'Valerie T. Yu', '666', '888', '我家牛排', 'A001'),
('G013', 'Chin-Wei Liao, Xian-Zhong Lin, Chun-Hsiu Yeh*, Yi-Teng Lin, Xuan Wang', 'Enhancing Real-Time PCB Defect Detection with YOLOv8 and a Lightweight FasterNet Backbone', '2025 IEEE 11th International Conference on Applied System Innovation (IEEE ICASI 2025)', '', '2025-04', 'Tokyo International Forum (東京国際フォーラム)', 'A002'),
('G014', 'Chin-Wei Laio, Xian-Zhong Lin, Chun-Hsiu Yeh*, Yi-Teng Lin, Xuan Wang', 'An Enhanced YOLO-Based Method for Accurate PCB Displacement Defect Detection Using Multi-Metric Fusion', '2025 IEEE 11th International Conference on Applied System Innovation (IEEE ICASI 2025)', '', ' 2025-04', 'Tokyo International Forum (東京国際フォーラム)', 'A002'),
('G015', 'Chun-Hsiu Yeh, Zhe-Rui Liu, Jun-Xiang Loe, Ching-Hsiang Chin, Chi-Siong Lee, Yuan-Yu Tsai', 'A Smart Traffic Violation Detection and Semi-Automated Reporting System Based on Deep Learning and Image Recognition', '2025 IEEE 11th International Conference on Applied System Innovation (IEEE ICASI 2025)', '', '2025-04', 'Tokyo International Forum (東京国際フォーラム)', 'A002'),
('G016', 'Yeh, Chun-Hsiu, Chang, Hui-Ching, Lin, Yi-Teng, and Chou, Yung-Chen*', 'A Novel Image Compression Scheme Based on Decision Tree Structure', 'The 20th International Conference on Intelligent Information Hiding and Multimedia Signal Processing', '', '2024-10', 'Matsue Terrsa, Japan', 'A002'),
('G017', 'Chung-Wei Kuo*, Chun-Chang Lin, Yu-Yi Hong, Jia-Ruei Liu, Chun-Hsiu Yeh, and Kuo-Yu Tsai', 'Research and Analysis of the Effects of Different Shielding Materials on Resisting Side-Channel Attacks on IoT Device Microcontroller', '2024 8th International Conference on Cryptography, Security and Privacy (CSP 2024), CSP 2024 conference proceedings', '', '2024-04', 'Ritsumeikan University Osaka campus', 'A002'),
('G018', 'ChunHsiu Yeh; Wei-Cheng Shen; Hung-Yu Chi; Chin-En Lin; Jong-Shin Chen', 'Enhancing Online Learning Monitoring with Novel Image Recognition Method Using Dlib for Eye Feature Detection', '2023 12th International Conference on Awareness Science and Technology (iCAST)', 'pp. 340-345', '2023-11', '朝陽科技大學', 'A002'),
('G019', 'ChunHsiu Yeh; Wei-Cheng Shen; Chi-Wei Ma; Qiu-Tong Yeh; Chung-Wei Kuo; Jong-Shin Chen', 'Real time Human Movement Recognition and Interaction in Virtual Fitness using Image Recognition and Motion Analysis', '023 12th International Conference on Awareness Science and Technology (iCAST)', '', '2023-11', 'Chaoyang University of Technology', 'A002');

-- --------------------------------------------------------

--
-- 資料表結構 `course`
--

CREATE TABLE `course` (
  `course_code` varchar(50) NOT NULL,
  `course_name` varchar(50) DEFAULT NULL,
  `credits` int(2) DEFAULT NULL,
  `required` varchar(30) DEFAULT NULL,
  `teacher_ID` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `courses`
--

CREATE TABLE `courses` (
  `courses_ID` varchar(50) NOT NULL COMMENT '課程ID',
  `name` varchar(200) DEFAULT NULL COMMENT '課程名稱',
  `class` varchar(200) DEFAULT NULL COMMENT '開課班級',
  `time` varchar(100) DEFAULT NULL COMMENT '時間',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `courses`
--

INSERT INTO `courses` (`courses_ID`, `name`, `class`, `time`, `pro_ID`) VALUES
('E001', '請益時間', '資電238', '13,14,17,18,56,57', 'A001'),
('E002', '人工智慧自然語言導論', '資訊三合', '19,58,59', 'A001'),
('E003', '人工智慧導論', '資訊三合', '22,23,24', 'A001'),
('E004', '專題研究(一)', '資訊三丁', '25,35', 'A001'),
('E005', '專題研究(一)', '資訊三丙', '25,35', 'A001'),
('E006', '專題研究(一)\r\n', '資訊三乙', '25,35', 'A001'),
('E007', '專題研究(一)', '資訊三甲', '25,35', 'A001'),
('E008', '自然語言處理', '人工智慧三學位學程', '26,27,28', 'A001'),
('E009', '生成式AI：文字與圖像生成的原理與實務', '人工智慧自然語言技術學分學', '29,210,211', 'A001'),
('E011', '請益時間', '資電239', '14,29,36,53,54', 'A002'),
('E064', '仲夏夜之戰爭', '114514', '41,42', 'A001'),
('E065', 'Apex', '我家', '63,64', 'A001'),
('E066', '睡吧', '資訊二丁', '31,32', 'A001'),
('E067', 'Valorant瞄準序訓練', '我家', '61,62', 'A001'),
('E068', '讓我看看', '彬彬', '612', 'A001'),
('E069', 'dd', 'vv', '63', 'A001'),
('E070', '專題研究(一)', '資訊三甲', '25,35', 'A002'),
('E071', '專題研究(一)', '資訊三乙', '25,35', 'A002'),
('E072', '專題研究(一)', '資訊三丙', '25,35', 'A002'),
('E073', '專題研究(一)', '資訊三丁', '25,35', 'A002'),
('E074', '資料庫系統', '資訊二丁', '17,56,57', 'A002'),
('E076', '資料庫系統', '資訊二乙', '18,19,52', 'A002');

-- --------------------------------------------------------

--
-- 資料表結構 `education`
--

CREATE TABLE `education` (
  `edu_ID` varchar(50) NOT NULL COMMENT '學歷ID',
  `department` varchar(100) DEFAULT NULL COMMENT '學歷系別',
  `degree` varchar(100) DEFAULT NULL COMMENT '學位',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `education`
--

INSERT INTO `education` (`edu_ID`, `department`, `degree`, `pro_ID`) VALUES
('B001', '國立中正大學 資訊工程研究所', '博士', 'A001'),
('B002', '國立中正大學 資訊工程研究所', '碩士', 'A001'),
('B003', '東海大學 資訊工程學系', '學士', 'A001'),
('B005', '歡樂研究所', '飛天小女警', 'A001'),
('B006', '國立中山大學', '專業獼猴驅趕員', 'A001'),
('B007', '別墅裡面', '唱K', 'A001'),
('B008', '國立中興大學 資訊科學與工程研究所', '博士', 'A002'),
('B009', '國立台灣講幹話大學', '幹話創作及研究學士學位學程', 'A004');

-- --------------------------------------------------------

--
-- 資料表結構 `experience`
--

CREATE TABLE `experience` (
  `experience_ID` varchar(50) NOT NULL COMMENT '經歷ID',
  `category` varchar(50) DEFAULT NULL COMMENT '經歷類別',
  `department` varchar(100) DEFAULT NULL COMMENT '單位',
  `position` varchar(100) DEFAULT NULL COMMENT '職位',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `experience`
--

INSERT INTO `experience` (`experience_ID`, `category`, `department`, `position`, `pro_ID`) VALUES
('D001', '校內', '資訊工程學系', '助理教授 沙沙給油', 'A001'),
('D002', '校內', '逢甲大學帆宣智慧城市5G實驗室', '研究員', 'A001'),
('D003', '校外', '中國唐山達創科技有限公司技術部', '技術顧問', 'A001'),
('D005', '校外', 'BTS', '顏值擔當', 'A001'),
('D006', '校內', '魅力無法黨', '黨衛軍首席', 'A001'),
('D007', '校內', '蘋果公司區域教育培訓中心', '教學組組長', 'A002'),
('D008', '校內', '資訊工程學系', '兼任副教授', 'A002'),
('D009', '校內', '資訊工程學系', '副教授', 'A002'),
('D010', '校外', '中州科技大學 多媒體與遊戲科學系', '副教授', 'A002'),
('D011', '校外', '中州科技大學 資訊管理系', '副教授', 'A002'),
('D012', '校外', '中州科技大學 資訊管理系', '助理教授', 'A002'),
('D013', '校外', '朝陽科技大學 資訊管理系', '講師', 'A002'),
('D014', '校外', '台灣百大企業股東', '全聯員工', 'A004');

-- --------------------------------------------------------

--
-- 資料表結構 `expertise`
--

CREATE TABLE `expertise` (
  `expertise_ID` varchar(50) NOT NULL COMMENT '專長ID',
  `item` varchar(100) DEFAULT NULL COMMENT '項目',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `expertise`
--

INSERT INTO `expertise` (`expertise_ID`, `item`, `pro_ID`) VALUES
('C001', '人工智慧\nArtificial Intelligence', 'A001'),
('C002', '自然語言處理\nNature Language Processing', 'A001'),
('C003', '中文故事分析與生成\nChinese Story Analysis and Generation', 'A001'),
('C004', '電腦視覺與人臉識別\r\nComputer Vision and Face Recognition', 'A001'),
('C006', '影像處理\r\nImage processing', 'A002'),
('C007', '智能製造\r\nSmart manufacturing', 'A002'),
('C008', '專案管理\r\nProject management', 'A002'),
('C009', '物件導向\r\nObject Oriented', 'A002'),
('C010', '水池裡面 in the pool', 'A001'),
('C011', '台灣講幹話大賽第一名', 'A004');

-- --------------------------------------------------------

--
-- 資料表結構 `journal`
--

CREATE TABLE `journal` (
  `jour_ID` varchar(50) NOT NULL COMMENT '期刊ID',
  `jour_character` varchar(100) DEFAULT NULL COMMENT '作者',
  `title` varchar(200) DEFAULT NULL COMMENT '論文標題',
  `name` varchar(200) DEFAULT NULL COMMENT '期刊名稱',
  `issue` varchar(100) DEFAULT NULL COMMENT '卷期',
  `date` varchar(50) DEFAULT NULL COMMENT '日期',
  `pages` varchar(50) DEFAULT NULL COMMENT '頁數',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `journal`
--

INSERT INTO `journal` (`jour_ID`, `jour_character`, `title`, `name`, `issue`, `date`, `pages`, `pro_ID`) VALUES
('F001', 'Jhe-Wei Lin, Tang-Wei Su and Che-Cheng Chang', 'Chinese Story Generation Based on Style Control of Transformer Model and Content Evaluation Method', 'Algorithms', 'Vol. 18, Iss. 3', '2025-03', NULL, 'A001'),
('F002', 'Jhe-Wei Lin*, Cheng-Hsuan Lee, Tang-Wei Su and Che-Cheng Chang', 'Importing Diffusion and Re-Designed Backward Process for Image De-Raining', 'Sensors', 'Vol. 24, Iss. 12', '2024-06', NULL, 'A001'),
('F003', 'Che-Cheng Chang*, Yee-Ming Ooi, Yu-Chun Chen and Jhe-Wei Li', 'Positioning Improvement with Multiple GPS Receivers Based on Shallow Asymmetric Neural Network', 'Electronics', 'Vol. 13, Iss. 3', '2024-01', NULL, 'A001'),
('F004', 'Jhe-Wei Lin, Tran Duy Thanh, Rong-Guey Chang', 'Multi-Channel of Word Embeddings for Sentiment Analysis', 'Soft Computing', '10.1007/s00500-022-07267-6', '2022-07', NULL, 'A001'),
('F005', 'Jhe-Wei Lin, Rong-Guey Chang', 'Chinese story generation of sentence format control based on multichannel word embedding and novel data format', 'Soft Computing', '10.1007/s00500-021-06548-w', '2022-01', NULL, 'A001'),
('F006', 'Jhe-Wei Lin, Cheng-Yan Siao, Rong-Guey Chang, Mei-Ling Hsu', 'Telemedicine System Based on Medical Consultation Assistance Integration', 'Journal of Software Engineering and Applications', 'DOI: 10.4236/jsea.2021.1410031', '2021-10', NULL, 'A001'),
('F007', 'Jhe-Wei Lin, Cheng-Yan Siao, Ting-Hsuan Chien, Rong-Guey Chang', 'A Novel Automatic Meal Delivery System', 'Intelligent Automation & Soft Computing', 'Vol.29, No.3', '2021-07', 'pp.685-695', 'A001'),
('F008', 'Cheng-Yan Siao, Jhe-Wei Lin, Ting-Hsuan Chien, Rong-Guey Chang', 'Paralleling Collision Detection on Five-axis Machining.', 'Intelligent Automation & Soft Computing', 'Vol.29, No.2', '2021-06', 'pp.559-56', 'A001'),
('F009', 'Elon Mask', 'I love you', 'Valorant瞄準序訓練', '114514', 'sdasd', '', 'A001'),
('F010', 'Jau-Ji Shen, Chun-Hsiu Yeh*, and Jinn-Ke Jan', 'A New Approach of Lossy Image Compression Based on Hybrid Image Resizing Techniques', 'The International Arab Journal of Information Technology', 'Vol. 16, No. 2', '2019-03', 'pp.226-235', 'A002'),
('F011', 'Yung-Chen Chou, Chun-Hsiu Yeh*, Jau-Ji Shen, and Jinn-Ke Jan', 'A New Blind Image Authentication for Image Tampering Detection and Recovery Based on Block-wise Feature Classification', 'Journal of Internet Technology', 'vol. 19, no. 6', '2018-11', 'pp. 1907-1917', 'A002'),
('F012', 'Shuchih Ernest Chang, Wei-Cheng Shen, and Chun-Hsiu Yeh', 'A Comparative Study of User Intention to Recommend Content on Mobile Social Networks', 'Multimedia tools and applications (MTAD)', 'Vol. 76, No. 4', 'Feb 2017, 2017-02', NULL, 'A002');

-- --------------------------------------------------------

--
-- 資料表結構 `lecture`
--

CREATE TABLE `lecture` (
  `lecture_ID` varchar(50) NOT NULL COMMENT '演講ID',
  `title` varchar(200) DEFAULT NULL COMMENT '演講題目',
  `location` varchar(50) DEFAULT NULL COMMENT '地點',
  `date` varchar(50) DEFAULT NULL COMMENT '日期',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `lecture`
--

INSERT INTO `lecture` (`lecture_ID`, `title`, `location`, `date`, `pro_ID`) VALUES
('J001', '我的自然語言之路，AI時代來臨:淺談CHATGPT的應用與影響', '國立北門高級中學', '2023-04', 'A001'),
('J002', 'I\'m good', '我家牛排', '2024-06', 'A001');

-- --------------------------------------------------------

--
-- 資料表結構 `professor`
--

CREATE TABLE `professor` (
  `pro_ID` varchar(50) NOT NULL COMMENT '教授ID',
  `name` varchar(50) DEFAULT NULL COMMENT '教授姓名',
  `role` varchar(50) DEFAULT NULL COMMENT '身分',
  `position` varchar(50) DEFAULT NULL COMMENT '職位',
  `introduction` text DEFAULT NULL COMMENT '自介',
  `email` varchar(100) DEFAULT NULL COMMENT 'email',
  `phone` varchar(100) DEFAULT NULL COMMENT '辦公室電話',
  `office` varchar(100) DEFAULT NULL COMMENT '辦公室位置',
  `photo` varchar(200) DEFAULT NULL COMMENT '照片'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `professor`
--

INSERT INTO `professor` (`pro_ID`, `name`, `role`, `position`, `introduction`, `email`, `phone`, `office`, `photo`) VALUES
('A001', '勒布朗占努斯', '專任教師', '嘿嘿嘿', '林老師畢業於國立中正大學資訊工程學系博士班，主要的研究領域包含人工智能、自然語言處理、中文故事生成以及電腦視覺等識別方法。在人工智慧與自然語言的研究主要專注在中文內文的解析與強化模型對句子的理解能力，並透過深度學習的方式來進行長短句子的生成與對話等應用。電腦視覺研究則是專注於輔助醫療等實際應用，在2020年林老師曾與嘉義大林慈濟醫院以及嘉義基督教醫院合作，利用影像辨識與深度學習的方法來建立口罩辨識系統以及營養機器人的實際應用。\r\n', 'jhewlin@fcu.edu.tw', '0424517250#3758', '台北101', 'uploads/none.jpg'),
('A002', '葉春秀', '專任教師', '副教授', '葉春秀老師畢業於國立中興大學資訊科學與工程研究所，1992年至2023年期間任職於中州科技大學資管系與多遊系擔任專任副教授。在教學上，曾榮獲優良導師、典範導師；帶領學生參與校內外專題競賽以及國內APP競賽獲獎多次。著作發表包括以影像處理技術應用於影像縮放技術與影像辨識、物件導向專案管理的技術開發，並曾獲ICAIT研討會最佳論文獎。主要的研究方向為專案管理、物件導向、影像辨識、智能製造。', 'chunhyeh@fcu.edu.tw', '0424517250#3736', '資電282', 'uploads/yei2-2.jpg'),
('A003', '林誠碩', '系主任', '壎黑大將軍', '', 'ONEPEACE@gmail.com', '902103590', '資電987', 'uploads/下載 (1).jpeg'),
('劉安之', '劉安之', '榮譽特聘講座', '榮譽特聘講座', '', 'acliu@fcu.edu.tw', '0424517250#3780', '', 'uploads/none.jpg'),
('吳振宇', '吳振宇', '行政人員', '書記', '負責職掌：大學部課務', 'wucy@o365.fcu.edu.tw', '', '', 'uploads/none.jpg'),
('孫雅麗', '孫雅麗', '特約講座', '特約講座', '孫教授現任台灣大學資訊管理學系教授，於資通安全領域之研究貢獻卓越。目前為行政院科技計畫首席評議專家、行政院科技會報兼任研究員、國家通訊傳播委員會（NCC）電信事業普及服務基金管理委員會委員、科技部政府科技發展計畫資通訊建設群組審議委員、台北市市政顧問等。 5G 通訊為本院的發展重點之一，擬借重孫教授的專長及經驗協助本院此領域的發展。\r\n\r\n孫教授曾任臺灣大學計算機及資訊網路中心主任、臺灣大學資訊管理系系主任兼所長、美國貝爾通訊研究室 (Bell Communications Research)研究員、科技部第二期能源國家型科技計畫能源主軸中心共同召集人、台北市悠遊卡投資控股股份有限公司董事等。\r\n\r\n孫教授獲獎無數，包括科技部工程司資訊安全實務研發計畫績優團隊獎、校服務傑出獎、校教學傑出獎、校教學優良獎等。', '', '', '', 'uploads/none.jpg'),
('張真誠', '張真誠', '講座教授', '何宜武先生學術講座', '張真誠講座教授，國際電子電機工程師學會會士(IEEE Fellow)、英國電機工程師學會會士(IET Fellow)、中華民國電腦學會會士(CS Fellow)與亞太人工智能學會會士(AAIA Fellow)，並獲選為歐洲科學院（英國）院士，歐洲科學與藝術研究院（奧地利）院士與美國國家人工智慧科學院院士。先後就讀於清華大學和交通大學計算機工程研究所，並以兩年時間榮獲全國首位計算機工程領域國家工學博士，並獲頒國立清華大學與國立交通大學傑出校友和國立中正大學名譽工學博士。曾任交通大學副教授，中興大學教授，清華大學教授，中正大學講座教授、工學院院長、教務長、代理校長和教育部顧問室主任等職，並獲邀擔任日本東京大學客座研究員、京都大學客座科學家。\r\n\r\n張教授對資訊安全、密碼學與多媒體圖像處理等領域有卓越貢獻，發表學術論文千餘篇、專著40冊，取得美中台專利36項，主持研究計畫百餘項。根據Google Scholar的統計，截至2024年十月共獲引用50,221次， H影響因子為103。2017年8月Guide2Research發布的全球計算機科學學術影響力排名，在一千名頂級科學家，張教授位列全台第一。2021年10月，美國史丹佛大學發佈全球前2%頂尖科學家榜單，張教授在人工智慧與影像處理領域25萬5千餘名科學家中生涯影響力名列全球第189名。張教授曾獲第一屆十大傑出資訊人才獎、資訊榮譽獎章、青年獎章、龍騰十傑獎、傑出工程教授獎、兩屆中山學術著作獎、連續五屆國科會傑出研究獎、東元科技獎、李遠哲傑出人才講座、潘文淵研究傑出獎、美國Journal of Systems and Software期刊全球前十五名頂尖學者獎、美國Pattern Recognition Letters期刊最佳引文獎、國科會傑出特約研究獎、英國IET學會首屆傑出研究獎、2012亞洲資訊安全終身成就獎等。\r\n\r\n多年來，張教授應國內外大學及國際會議邀請，發表學術演講千餘場，獲聘北京大學、清華大學、浙江大學、上海交大、華中科大、武漢大學、香港理工大學等名校之客座教授、名譽教授和講席教授等。', 'alan3c@gmail.com', '0424517250#3790', '', 'uploads/none.jpg'),
('李榮三', '李榮三', '特聘教授', '特聘教授', '', 'leejs@fcu.edu.tw', '0424517250#3767', '', 'uploads/none.jpg'),
('楊晴雯', '楊晴雯', '兼任教師', '兼任教授', '', 'cwhello7@gmail.com', '', '', 'uploads/none.jpg'),
('竇其仁', '竇其仁', '特約講座', '特聘教授', '', 'crdow@fcu.edu.tw', '0424517250#3739', '', 'uploads/none.jpg'),
('羅浩榮', '羅浩榮', '退休教師', '退休教授', '', '', '', '', 'uploads/none.jpg');

-- --------------------------------------------------------

--
-- 資料表結構 `project`
--

CREATE TABLE `project` (
  `project_ID` varchar(50) NOT NULL COMMENT '計畫ID',
  `category` varchar(50) DEFAULT NULL COMMENT '計畫類別',
  `name` varchar(200) DEFAULT NULL COMMENT '計畫名稱',
  `date` varchar(50) DEFAULT NULL COMMENT '日期',
  `number` varchar(100) DEFAULT NULL COMMENT '計畫編號',
  `role` varchar(50) DEFAULT NULL COMMENT '計畫角色',
  `pro_ID` varchar(50) DEFAULT NULL COMMENT '教授ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `project`
--

INSERT INTO `project` (`project_ID`, `category`, `name`, `date`, `number`, `role`, `pro_ID`) VALUES
('H001', '國科會計畫', '遷入式設備之輕量化技術AOI檢測模型優化及自動適配鏡頭系統研究與開發', '2024-08~2025-07', 'NSTC113-2221-E-035-059-', '主持人', 'A001'),
('H002', '國科會計畫', '車流辨識與智能交通系統優化', '2024-07~2025-02', '113-2813-C-035-129-E', '主持人', 'A001'),
('H003', '國科會計畫', '創新學習之生成式人工智慧在智慧教育的應用', '2024-07~2025-02', '113-2813-C-035-039-E', '主持人', 'A001'),
('H004', '國科會計畫', '基於人臉超解析度還原結合人臉識別之整合系', '2022-12~2023-11', 'NSTC111-2222-E-035-010-', '主持人', 'A001'),
('H005', '產學合作計畫', '第二期新工程教育方法實驗與建構計畫(A類)-全面課程地圖與學習架構調整計畫', '2025-02~2027-01', NULL, '共同主持人', 'A001'),
('H006', '產學合作計畫', '超解析度之技術應用於製鞋影像', '2024-08~2025-07', NULL, '主持人', 'A001'),
('H007', '產學合作計畫', '瑕疵檢測平臺開發', '2024-03~2025-03', NULL, '主持人', 'A001'),
('H008', '產學合作計畫', '(國合)Design and Development of NX SAAS Application Cloud Services NX SAAS應用雲服務的設計與開發 Ⅴ', '2022-04~2023-03', NULL, '協同主持人', 'A001');

-- --------------------------------------------------------

--
-- 資料表結構 `teacher`
--

CREATE TABLE `teacher` (
  `teacher_ID` varchar(50) NOT NULL,
  `teacher_name` varchar(50) DEFAULT NULL,
  `research_room_ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `員工1`
--

CREATE TABLE `員工1` (
  `編號` varchar(5) NOT NULL,
  `姓名` varchar(8) NOT NULL,
  `部門` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `員工1`
--

INSERT INTO `員工1` (`編號`, `姓名`, `部門`) VALUES
('S0001', '一心', '銷售部'),
('S0002', '二聖', '生產部'),
('S0003', '三多', '銷售部'),
('S0004', '四維', '生產部'),
('S0005', '五福', '銷售部');

-- --------------------------------------------------------

--
-- 資料表結構 `產品`
--

CREATE TABLE `產品` (
  `品號` varchar(5) NOT NULL,
  `品名` varchar(8) NOT NULL,
  `定價` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `產品`
--

INSERT INTO `產品` (`品號`, `品名`, `定價`) VALUES
('P0001', '筆電', 30000),
('P0002', '滑鼠', 1000),
('P0003', '手機', 15000),
('P0004', '硬碟', 2500),
('P0005', '手錶', 3000),
('P0006', '耳機', 1200);

-- --------------------------------------------------------

--
-- 資料表結構 `銷售`
--

CREATE TABLE `銷售` (
  `編號` varchar(5) NOT NULL,
  `品號` varchar(5) NOT NULL,
  `數量` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `銷售`
--

INSERT INTO `銷售` (`編號`, `品號`, `數量`) VALUES
('S0001', 'P0001', 56),
('S0001', 'P0005', 73),
('S0002', 'P0002', 92),
('S0002', 'P0005', 0),
('S0003', 'P0004', 92),
('S0003', 'P0005', 70),
('S0004', 'P0003', 75),
('S0004', 'P0004', 88),
('S0004', 'P0005', 68),
('S0005', 'P0005', 95);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`username`);

--
-- 資料表索引 `award`
--
ALTER TABLE `award`
  ADD PRIMARY KEY (`award_ID`);

--
-- 資料表索引 `conference`
--
ALTER TABLE `conference`
  ADD PRIMARY KEY (`conf_ID`);

--
-- 資料表索引 `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_code`);

--
-- 資料表索引 `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courses_ID`);

--
-- 資料表索引 `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`edu_ID`);

--
-- 資料表索引 `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`experience_ID`);

--
-- 資料表索引 `expertise`
--
ALTER TABLE `expertise`
  ADD PRIMARY KEY (`expertise_ID`);

--
-- 資料表索引 `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`jour_ID`);

--
-- 資料表索引 `lecture`
--
ALTER TABLE `lecture`
  ADD PRIMARY KEY (`lecture_ID`);

--
-- 資料表索引 `professor`
--
ALTER TABLE `professor`
  ADD PRIMARY KEY (`pro_ID`);

--
-- 資料表索引 `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_ID`);

--
-- 資料表索引 `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_ID`);

--
-- 資料表索引 `員工1`
--
ALTER TABLE `員工1`
  ADD PRIMARY KEY (`編號`);

--
-- 資料表索引 `產品`
--
ALTER TABLE `產品`
  ADD PRIMARY KEY (`品號`);

--
-- 資料表索引 `銷售`
--
ALTER TABLE `銷售`
  ADD PRIMARY KEY (`編號`,`品號`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
