-- 插入教授基本資料
INSERT INTO professor (pro_ID, name, department, introduction, photo) VALUES 
('A001', '林哲維', '資訊工程學系', '林老師畢業於國立中正大學資訊工程學系博士班，主要的研究領域包含人工智能、自然語言處理、中文故事生成以及電腦視覺等識別方法。在人工智慧與自然語言的研究主要專注在中文內文的解析與強化模型對句子的理解能力，並透過深度學習的方式來進行長短句子的生成與對話等應用。電腦視覺研究則是專注於輔助醫療等實際應用，在2020年林老師曾與嘉義大林慈濟醫院以及嘉義基督教醫院合作，利用影像辨識與深度學習的方法來建立口罩辨識系統以及營養機器人的實際應用。', 'uploads/teacher_photo.jpg');

-- 插入學歷資料
INSERT INTO education (edu_ID, pro_ID, department, degree) VALUES 
('E001', 'A001', '資訊工程研究所', '博士'),
('E002', 'A001', '資訊工程研究所', '碩士'),
('E003', 'A001', '資訊工程學系', '學士');

-- 插入專長資料
INSERT INTO expertise (expertise_ID, pro_ID, item) VALUES 
('S001', 'A001', '人工智慧'),
('S002', 'A001', '自然語言處理'),
('S003', 'A001', '中文故事分析與生成'),
('S004', 'A001', '電腦視覺與人臉識別');

-- 插入期刊論文
INSERT INTO journal (jour_ID, pro_ID, jour_character, title, name, issue, date, pages) VALUES 
('J001', 'A001', 'SCIE', 'Chinese Story Generation Based on Style Control...', 'Algorithms', '2025-03', '2025-03-01', 1),
('J002', 'A001', 'SCIE', 'Importing Diffusion and Re-Designed...', 'Sensors', '2024-06', '2024-06-01', 1),
('J003', 'A001', 'SCIE', 'Positioning Improvement with Multiple GPS...', 'Electronics', '2024-01', '2024-01-01', 1);

-- 插入會議論文
INSERT INTO conference (conf_ID, pro_ID, conf_character, title, name, pages, date, location) VALUES 
('C001', 'A001', 'IEEE', 'Importing Diffusion and Neural Styles...', 'IEEE ECICE', 1, '2023-10-01', '線上會議'),
('C002', 'A001', 'ICASI', 'Evaluate the Chinese Story Cycle Generation...', 'ICASI', 1, '2022-04-01', '台北'),
('C003', 'A001', 'IEEE', 'Nutritionist based on Deep Learning', 'IEEE ICASI', 1, '2021-09-01', '線上會議');

-- 插入經歷
INSERT INTO experience (experience_ID, pro_ID, sort, department, position) VALUES 
('X001', 'A001', '1', '資訊工程學系', '助理教授'),
('X002', 'A001', '2', '逢甲大學帆宣智慧城市5G實驗室', '研究員'),
('X003', 'A001', '3', '中國唐山達創科技有限公司 技術部', '技術顧問'); 