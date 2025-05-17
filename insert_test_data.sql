-- 插入教師基本資料
INSERT INTO teacher_info (id, name, title, intro, email, phone, photo_path) VALUES 
(1, '林哲維', '助理教授', '林老師畢業於國立中正大學資訊工程學系博士班，主要的研究領域包含人工智能、自然語言處理、中文故事生成以及電腦視覺等識別方法。在人工智慧與自然語言的研究主要專注在中文內文的解析與強化模型對句子的理解能力，並透過深度學習的方式來進行長短句子的生成與對話等應用。電腦視覺研究則是專注於輔助醫療等實際應用，在2020年林老師曾與嘉義大林慈濟醫院以及嘉義基督教醫院合作，利用影像辨識與深度學習的方法來建立口罩辨識系統以及營養機器人的實際應用。', 'jhewlin@fcu.edu.tw', '0424517250#3758', 'uploads/teacher_photo.jpg');

-- 插入學歷資料
INSERT INTO education (teacher_id, degree, school, department) VALUES 
(1, '博士', '國立中正大學', '資訊工程研究所'),
(1, '碩士', '國立中正大學', '資訊工程研究所'),
(1, '學士', '東海大學', '資訊工程學系');

-- 插入專長資料
INSERT INTO specialties (teacher_id, specialty, specialty_en) VALUES 
(1, '人工智慧', 'Artificial Intelligence'),
(1, '自然語言處理', 'Natural Language Processing'),
(1, '中文故事分析與生成', 'Chinese Story Analysis and Generation'),
(1, '電腦視覺與人臉識別', 'Computer Vision and Face Recognition');

-- 插入期刊論文
INSERT INTO journal_papers (teacher_id, title, journal, publish_date, type) VALUES 
(1, 'Chinese Story Generation Based on Style Control...', 'Algorithms', '2025-03-01', 'SCIE'),
(1, 'Importing Diffusion and Re-Designed...', 'Sensors', '2024-06-01', 'SCIE'),
(1, 'Positioning Improvement with Multiple GPS...', 'Electronics', '2024-01-01', 'SCIE');

-- 插入會議論文
INSERT INTO conference_papers (teacher_id, title, conference, publish_date) VALUES 
(1, 'Importing Diffusion and Neural Styles...', 'IEEE ECICE', '2023-10-01'),
(1, 'Evaluate the Chinese Story Cycle Generation...', 'ICASI', '2022-04-01'),
(1, 'Nutritionist based on Deep Learning', 'IEEE ICASI', '2021-09-01');

-- 插入經歷
INSERT INTO experiences (teacher_id, position, organization, is_internal) VALUES 
(1, '助理教授', '資訊工程學系', TRUE),
(1, '研究員', '逢甲大學帆宣智慧城市5G實驗室', TRUE),
(1, '技術顧問', '中國唐山達創科技有限公司 技術部', FALSE); 