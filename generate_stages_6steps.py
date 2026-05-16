#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os

stages_data = [
    {
        'stage_num': 2,
        'filename': 'stage-2-maghreb.html',
        'title': '🌍 Stage 2: Maghreb',
        'subtitle': 'اكتشف بلاد المغرب - الجمال والتضاريس المتنوعة',
        'folder': 'f2',
        'pdf': 'cours2.pdf'
    },
    {
        'stage_num': 3,
        'filename': 'stage-3-africa.html',
        'title': '🌍 Stage 3: Africa',
        'subtitle': 'اكتشف القارة الأفريقية - التنوع الثقافي والطبيعي',
        'folder': 'f3',
        'pdf': 'cours3.pdf'
    },
    {
        'stage_num': 4,
        'filename': 'stage-4-europe.html',
        'title': '🌍 Stage 4: Europe',
        'subtitle': 'اكتشف أوروبا - حضارة وثقافة وتاريخ عريق',
        'folder': 'f4',
        'pdf': 'cours2.pdf'
    },
    {
        'stage_num': 5,
        'filename': 'stage-5-asia.html',
        'title': '🌍 Stage 5: Asia',
        'subtitle': 'اكتشف آسيا - القارة الأكبر والأكثر تنوعاً',
        'folder': 'f5',
        'pdf': 'cours5.pdf'
    },
    {
        'stage_num': 6,
        'filename': 'stage-6-namerica.html',
        'title': '🌍 Stage 6: North America',
        'subtitle': 'اكتشف أمريكا الشمالية - الطبيعة والحضارة',
        'folder': 'f6',
        'pdf': 'cours6.pdf'
    },
    {
        'stage_num': 7,
        'filename': 'stage-7-samerica.html',
        'title': '🌍 Stage 7: South America',
        'subtitle': 'اكتشف أمريكا الجنوبية - الغابات والثقافات',
        'folder': 'f7',
        'pdf': 'cours7.pdf'
    }
]

template = '''<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{TITLE}} - 6 Étapes Complètes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ROOT VARIABLES */
        :root {
            --candy-red:    #E91E63;
            --candy-orange: #FF9800;
            --candy-yellow: #FFD54F;
            --candy-green:  #26C281;
            --candy-teal:   #00BCD4;
            --candy-blue:   #2196F3;
            --candy-purple: #9C27B0;
            --candy-pink:   #EC407A;
            --bg-deep:    #0D0221;
            --bg-mid:     #1A0033;
            --bg-card:    #2B0A54;
            --text-light: #FFFFFF;
            --text-muted: rgba(255,255,255,0.65);
            --border-r: 20px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Nunito', sans-serif; background: var(--bg-deep); color: var(--text-light); overflow-x: hidden; }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 15% 20%, #FF336622 0%, transparent 40%),
                        radial-gradient(circle at 85% 70%, #00AAFF22 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        .stage-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; position: relative; z-index: 1; }

        .stage-header {
            text-align: center;
            margin-bottom: 50px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .stage-header h1 {
            font-size: 2.8rem;
            color: var(--candy-yellow);
            margin-bottom: 15px;
            animation: slideIn 0.6s ease-out;
        }

        .stage-header p { font-size: 1.2rem; opacity: 0.9; color: var(--text-muted); animation: slideIn 0.7s ease-out; }

        .resources {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .resource-display {
            background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-mid) 100%);
            padding: 15px 25px;
            border-radius: 15px;
            border: 2px solid var(--candy-yellow);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            color: var(--candy-yellow);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            animation: scaleIn 0.4s ease-out;
            transition: all 0.3s;
        }

        .resource-display:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
            animation: bounce 0.6s ease-in-out;
        }

        .steps-progress {
            display: flex;
            justify-content: space-around;
            margin: 40px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .step {
            flex: 1;
            min-width: 130px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-mid) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 10px;
            border: 3px solid var(--candy-purple);
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(156, 39, 176, 0.3);
        }

        .step.active .step-icon {
            background: linear-gradient(135deg, var(--candy-purple) 0%, #E91E63 100%);
            border-color: var(--candy-yellow);
            transform: scale(1.15);
            box-shadow: 0 0 30px rgba(156, 39, 176, 0.6);
        }

        .step.completed .step-icon {
            background: linear-gradient(135deg, var(--candy-green) 0%, #26C281 100%);
            border-color: var(--candy-green);
        }

        .step-label {
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 8px;
        }

        .step.active .step-label, .step.completed .step-label { color: var(--candy-yellow); }

        .stage-content {
            background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-mid) 100%);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 50px rgba(156, 39, 176, 0.3);
            border: 2px solid rgba(156, 39, 176, 0.2);
            min-height: 500px;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 10px 50px rgba(156, 39, 176, 0.3), inset 0 0 40px rgba(156, 39, 176, 0.1); }
            50% { box-shadow: 0 10px 60px rgba(156, 39, 176, 0.5), inset 0 0 40px rgba(156, 39, 176, 0.15); }
        }

        .step-content { display: none; animation: slideIn 0.6s ease-out; }
        .step-content.active { display: block; }

        .step-content h2 {
            font-size: 2rem;
            color: var(--candy-yellow);
            margin-bottom: 25px;
            text-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
        }

        .image-container, .pdf-container {
            width: 100%;
            max-height: 600px;
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            margin-bottom: 25px;
            border: 3px solid var(--candy-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: slideIn 0.6s ease-out;
        }

        .image-container img, .pdf-viewer {
            width: 100%;
            height: 100%;
            object-fit: contain;
            animation: scaleIn 0.7s ease-out;
        }

        .text-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 20px;
            animation: slideRight 0.5s ease-out;
        }

        .text-content h3 {
            color: var(--candy-yellow);
            font-size: 1.4rem;
            margin: 20px 0 10px;
        }

        .qcm-container { display: flex; flex-direction: column; gap: 25px; }

        .question-card {
            background: rgba(156, 39, 176, 0.1);
            border-left: 5px solid var(--candy-purple);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid rgba(156, 39, 176, 0.3);
            animation: slideRight 0.5s ease-out;
            transition: all 0.3s;
        }

        .question-card:hover {
            border: 2px solid var(--candy-purple);
            box-shadow: 0 4px 20px rgba(156, 39, 176, 0.3);
        }

        .question-card.correct {
            background: rgba(38, 194, 129, 0.1);
            border-left: 5px solid var(--candy-green);
            border: 2px solid var(--candy-green);
            animation: pulse 0.5s ease-out;
        }

        .question-card.incorrect {
            background: rgba(233, 30, 99, 0.1);
            border-left: 5px solid var(--candy-red);
            border: 2px solid var(--candy-red);
            animation: shake 0.5s ease-out;
        }

        .question-card h3 {
            color: var(--candy-yellow);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .option-btn {
            background: rgba(255, 215, 0, 0.05);
            border: 2px solid rgba(255, 215, 0, 0.3);
            color: var(--text-light);
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            text-align: right;
            animation: slideRight 0.4s ease-out;
        }

        .option-btn:hover {
            background: rgba(255, 215, 0, 0.15);
            border-color: var(--candy-yellow);
            transform: translateX(-5px);
        }

        .option-btn.selected {
            background: var(--candy-yellow);
            color: var(--bg-deep);
            border-color: var(--candy-yellow);
            font-weight: 700;
            transform: scale(1.02);
        }

        .option-btn.disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .option-btn.correct-answer {
            background: var(--candy-green) !important;
            color: white !important;
            border-color: var(--candy-green) !important;
            animation: pulse 0.5s ease-out;
        }

        .option-btn.wrong-answer {
            background: var(--candy-red) !important;
            color: white !important;
            border-color: var(--candy-red) !important;
            animation: shake 0.3s ease-out;
        }

        .answer-explanation {
            color: var(--candy-green);
            margin-top: 15px;
            font-size: 0.95rem;
            font-weight: 600;
            display: none;
            animation: slideRight 0.5s ease-out;
        }

        .answer-explanation.show {
            display: block;
        }

        .verify-btn {
            background: linear-gradient(135deg, var(--candy-blue) 0%, var(--candy-teal) 100%);
            color: var(--text-light);
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s;
            display: inline-block;
            animation: slideRight 0.5s ease-out;
        }

        .verify-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 188, 212, 0.4);
        }

        .verify-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        textarea {
            width: 100%;
            height: 300px;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid var(--candy-purple);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            animation: slideIn 0.5s ease-out;
            transition: all 0.3s;
        }

        textarea:focus {
            border-color: var(--candy-blue);
            box-shadow: 0 0 15px rgba(33, 150, 243, 0.3);
            outline: none;
        }

        .step-controls {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            justify-content: center;
            flex-wrap: wrap;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn {
            background: linear-gradient(135deg, var(--candy-purple) 0%, var(--candy-pink) 100%);
            color: var(--text-light);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(156, 39, 176, 0.4);
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            animation: scaleIn 0.4s ease-out;
        }

        .btn:hover {
            transform: translateY(-3px) translateX(0);
            box-shadow: 0 6px 20px rgba(156, 39, 176, 0.6);
            animation: bounce 0.6s ease-in-out;
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn-prev { background: linear-gradient(135deg, var(--candy-blue) 0%, var(--candy-teal) 100%); }

        #evaluation-area {
            margin-top: 30px;
            padding: 25px;
            background: linear-gradient(135deg, rgba(38, 194, 129, 0.2) 0%, rgba(76, 175, 80, 0.1) 100%);
            border-radius: 12px;
            border: 2px solid var(--candy-green);
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        #evaluation-area h2 {
            color: var(--candy-green);
            margin-bottom: 15px;
            animation: scaleIn 0.4s ease-out;
        }

        #evaluation-area .reward {
            font-size: 1.5rem;
            margin: 10px 0;
            color: var(--candy-yellow);
            animation: slideRight 0.5s ease-out;
        }

        @media (max-width: 768px) {
            .stage-header h1 { font-size: 1.8rem; }
            .step-icon { width: 60px; height: 60px; font-size: 1.8rem; }
            .stage-content { padding: 25px; }
            .steps-progress { gap: 10px; }
            .step { min-width: 100px; }
        }
    </style>
</head>
<body>
    <div class="stage-container">
        <!-- Header -->
        <div class="stage-header">
            <h1>{{TITLE}}</h1>
            <p>{{SUBTITLE}}</p>
        </div>

        <!-- Resources -->
        <div class="resources">
            <div class="resource-display"><span>💎</span><span id="diamonds-count">150</span></div>
            <div class="resource-display"><span>🪙</span><span id="coins-count">750</span></div>
        </div>

        <!-- Steps Progress -->
        <div class="steps-progress">
            <div class="step active" onclick="goToStep(1)"><div class="step-icon">🖼️</div><div class="step-label">البداية</div></div>
            <div class="step" onclick="goToStep(2)"><div class="step-icon">🎨</div><div class="step-label">الصورة 1</div></div>
            <div class="step" onclick="goToStep(3)"><div class="step-icon">📸</div><div class="step-label">الصورة 2</div></div>
            <div class="step" onclick="goToStep(4)"><div class="step-icon">📚</div><div class="step-label">الدرس</div></div>
            <div class="step" onclick="goToStep(5)"><div class="step-icon">❓</div><div class="step-label">اختبار</div></div>
            <div class="step" onclick="goToStep(6)"><div class="step-icon">✍️</div><div class="step-label">الكتابة</div></div>
        </div>

        <!-- Content -->
        <div class="stage-content">
            <!-- Step 1: Image Début -->
            <div class="step-content active">
                <h2>🖼️ الخطوة 1: ابدأ رحلتك</h2>
                <div class="image-container">
                    <img src="assets/stage/{{FOLDER}}/debut.png" alt="صورة البداية">
                </div>
                <div class="text-content">
                    <h3>مرحباً بك في رحلة تعليمية رائعة!</h3>
                    <p>هذه الصورة تمثل بداية رحلتك في فهم هذه المنطقة الجغرافية وأهميتها. ستتعلم في هذه المرحلة المفاهيم الأساسية التي ستساعدك على فهم العالم من حولك بطريقة أفضل.</p>
                </div>
            </div>

            <!-- Step 2: Image V1 -->
            <div class="step-content">
                <h2>🎨 الخطوة 2: الصور التوضيحية (V1)</h2>
                <div class="image-container">
                    <img src="assets/stage/{{FOLDER}}/v.jpg" alt="صورة توضيحية v1">
                </div>
                <div class="text-content">
                    <h3>فهم أعمق للمنطقة</h3>
                    <p>هذه الصورة تقدم لك رؤية مفصلة عن الخصائص الجغرافية لهذه المنطقة. لاحظ العناصر المختلفة وكيفية تفاعلها مع بعضها البعض.</p>
                </div>
            </div>

            <!-- Step 3: Image P -->
            <div class="step-content">
                <h2>📸 الخطوة 3: معلومات متقدمة (P)</h2>
                <div class="image-container">
                    <img src="assets/stage/{{FOLDER}}/p.png" alt="صورة توضيحية p">
                </div>
                <div class="text-content">
                    <h3>تفاصيل إضافية مهمة</h3>
                    <p>هذه الصورة توفر معلومات إضافية وتفاصيل متقدمة حول الموضوع. ركز على النقاط الرئيسية وحاول ربطها بما تعلمته في الخطوات السابقة.</p>
                </div>
            </div>

            <!-- Step 4: PDF -->
            <div class="step-content">
                <h2>📚 الخطوة 4: الدرس الكامل</h2>
                <div class="pdf-container">
                    <iframe class="pdf-viewer" src="assets/stage/{{FOLDER}}/{{PDF}}"></iframe>
                </div>
                <div style="margin-top: 20px; padding: 15px; background: rgba(33, 150, 243, 0.1); border-radius: 10px; border-left: 4px solid var(--candy-blue);">
                    <p style="color: var(--text-light);">📖 اقرأ الدرس بعناية وركز على النقاط الرئيسية قبل الانتقال إلى الاختبار.</p>
                </div>
            </div>

            <!-- Step 5: QCM -->
            <div class="step-content">
                <h2>❓ الخطوة 5: اختبر معلوماتك</h2>
                <div class="qcm-container">
                    <!-- Question 1 -->
                    <div class="question-card" data-question="q1" data-answer="ب">
                        <h3>السؤال 1: ما هو المجال الجغرافي؟</h3>
                        <div class="options">
                            <button class="option-btn" onclick="selectOption(this, 'q1')">أ) مساحة خاصة بالزراعة فقط</button>
                            <button class="option-btn" onclick="selectOption(this, 'q1')">ب) رقعة محددة لدولة لها حدود وخصائص مختلفة</button>
                            <button class="option-btn" onclick="selectOption(this, 'q1')">ج) مكان خالٍ من السكان</button>
                        </div>
                        <button class="verify-btn" onclick="verifyAnswer('q1')">✓ تحقق من الإجابة</button>
                        <div class="answer-explanation"></div>
                    </div>

                    <!-- Question 2 -->
                    <div class="question-card" data-question="q2" data-answer="ب">
                        <h3>السؤال 2: ما الذي يشمله المجال الطبيعي؟</h3>
                        <div class="options">
                            <button class="option-btn" onclick="selectOption(this, 'q2')">أ) المدارس والمصانع</button>
                            <button class="option-btn" onclick="selectOption(this, 'q2')">ب) الصخور والهواء والماء</button>
                            <button class="option-btn" onclick="selectOption(this, 'q2')">ج) الطرقات فقط</button>
                        </div>
                        <button class="verify-btn" onclick="verifyAnswer('q2')">✓ تحقق من الإجابة</button>
                        <div class="answer-explanation"></div>
                    </div>

                    <!-- Question 3 -->
                    <div class="question-card" data-question="q3" data-answer="ب">
                        <h3>السؤال 3: ماذا يمثل المجال الجغرافي كمنتوج اجتماعي وثقافي؟</h3>
                        <div class="options">
                            <button class="option-btn" onclick="selectOption(this, 'q3')">أ) فقط التضاريس الطبيعية</button>
                            <button class="option-btn" onclick="selectOption(this, 'q3')">ب) العلاقات الاجتماعية والقيم والعادات</button>
                            <button class="option-btn" onclick="selectOption(this, 'q3')">ج) المناخ فقط</button>
                        </div>
                        <button class="verify-btn" onclick="verifyAnswer('q3')">✓ تحقق من الإجابة</button>
                        <div class="answer-explanation"></div>
                    </div>

                    <!-- Question 4 -->
                    <div class="question-card" data-question="q4" data-answer="ب">
                        <h3>السؤال 4: ما هو المجال الجوي؟</h3>
                        <div class="options">
                            <button class="option-btn" onclick="selectOption(this, 'q4')">أ) مساحة اليابسة</button>
                            <button class="option-btn" onclick="selectOption(this, 'q4')">ب) الفضاء الجوي الخاص بدولة</button>
                            <button class="option-btn" onclick="selectOption(this, 'q4')">ج) مياه البحار</button>
                        </div>
                        <button class="verify-btn" onclick="verifyAnswer('q4')">✓ تحقق من الإجابة</button>
                        <div class="answer-explanation"></div>
                    </div>

                    <!-- Question 5 -->
                    <div class="question-card" data-question="q5" data-answer="ج">
                        <h3>السؤال 5: ما هو المجال البحري؟</h3>
                        <div class="options">
                            <button class="option-btn" onclick="selectOption(this, 'q5')">أ) الأراضي الزراعية</button>
                            <button class="option-btn" onclick="selectOption(this, 'q5')">ب) الفضاء الجوي</button>
                            <button class="option-btn" onclick="selectOption(this, 'q5')">ج) أجزاء من البحار تابعة لدولة</button>
                        </div>
                        <button class="verify-btn" onclick="verifyAnswer('q5')">✓ تحقق من الإجابة</button>
                        <div class="answer-explanation"></div>
                    </div>
                </div>
            </div>

            <!-- Step 6: Writing -->
            <div class="step-content">
                <h2>✍️ الخطوة 6: اكتب فقرتك</h2>
                <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 1.05rem;">
                    📝 اكتب فقرة مختصرة (100-200 كلمة) عن المجالات الجغرافية استناداً إلى ما تعلمته:
                </p>
                <textarea id="student-essay" placeholder="اكتب هنا ما تعلمته..."></textarea>
                <div style="margin-top: 20px; padding: 15px; background: rgba(156, 39, 176, 0.1); border-radius: 10px; border-left: 4px solid var(--candy-purple);">
                    <p style="color: var(--text-light);">💡 استخدم جملاً واضحة واشرح بكلماتك الخاصة ما تعلمته.</p>
                </div>
                <div id="evaluation-area" style="display: none;"></div>
            </div>
        </div>

        <!-- Controls -->
        <div class="step-controls">
            <button class="btn btn-prev" onclick="previousStep()" style="display: none;" id="prevBtn">⬅️ السابق</button>
            <button class="btn" onclick="nextStep()" id="nextBtn">التالي ➡️</button>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 6;
        const selectedAnswers = {};
        const answeredQuestions = {
            q1: false, q2: false, q3: false, q4: false, q5: false
        };

        // Correct answers
        const correctAnswers = {
            q1: 'ب',
            q2: 'ب',
            q3: 'ب',
            q4: 'ب',
            q5: 'ج'
        };

        // Explanations
        const explanations = {
            q1: {
                correct: '✓ أحسنت! المجال الجغرافي هو رقعة محددة لدولة لها حدود وخصائص مختلفة.',
                incorrect: '✗ للأسف، الإجابة غير صحيحة. المجال الجغرافي هو رقعة محددة لدولة لها حدود وخصائص مختلفة.'
            },
            q2: {
                correct: '✓ أحسنت! المجال الطبيعي يشمل الصخور والهواء والماء.',
                incorrect: '✗ للأسف، الإجابة غير صحيحة. المجال الطبيعي يشمل الصخور والهواء والماء.'
            },
            q3: {
                correct: '✓ أحسنت! المجال الجغرافي كمنتوج اجتماعي وثقافي يمثل العلاقات الاجتماعية والقيم والعادات.',
                incorrect: '✗ للأسف، الإجابة غير صحيحة. المجال الجغرافي كمنتوج اجتماعي وثقافي يمثل العلاقات الاجتماعية والقيم والعادات.'
            },
            q4: {
                correct: '✓ أحسنت! المجال الجوي هو الفضاء الجوي الخاص بدولة.',
                incorrect: '✗ للأسف، الإجابة غير صحيحة. المجال الجوي هو الفضاء الجوي الخاص بدولة.'
            },
            q5: {
                correct: '✓ أحسنت! المجال البحري هو أجزاء من البحار تابعة لدولة.',
                incorrect: '✗ للأسف، الإجابة غير صحيحة. المجال البحري هو أجزاء من البحار تابعة لدولة.'
            }
        };

        const gameState = {
            userId: sessionStorage.getItem('userId') || 'user-' + Date.now(),
            diamonds: 150,
            coins: 750,
            completedSteps: []
        };

        function goToStep(step) {
            if (step >= 1 && step <= totalSteps) {
                currentStep = step;
                updateUI();
            }
        }

        function nextStep() {
            if (currentStep === 5) {
                // Check if all QCM questions are answered
                const allAnswered = ['q1', 'q2', 'q3', 'q4', 'q5'].every(q => answeredQuestions[q]);
                if (!allAnswered) {
                    alert('يرجى الإجابة على جميع الأسئلة قبل المتابعة! 📚');
                    return;
                }
            }
            if (currentStep < totalSteps) {
                currentStep++;
                updateUI();
            } else if (currentStep === totalSteps) {
                evaluateEssay();
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        }

        function updateUI() {
            document.querySelectorAll('.step-content').forEach((el, idx) => {
                el.classList.toggle('active', idx + 1 === currentStep);
            });

            document.querySelectorAll('.step').forEach((el, idx) => {
                const stepNum = idx + 1;
                el.classList.toggle('active', stepNum === currentStep);
            });

            document.getElementById('prevBtn').style.display = currentStep > 1 ? 'flex' : 'none';
            document.getElementById('nextBtn').textContent = currentStep === totalSteps ? '✅ تقييم عملي' : 'التالي ➡️';
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function selectOption(btn, questionId) {
            const questionCard = btn.closest('.question-card');
            btn.parentElement.querySelectorAll('.option-btn').forEach(b => {
                b.classList.remove('selected');
                b.classList.remove('disabled');
            });
            btn.classList.add('selected');
            selectedAnswers[questionId] = btn.textContent.trim().charAt(0);
        }

        function verifyAnswer(questionId) {
            if (!selectedAnswers[questionId]) {
                alert('يرجى اختيار إجابة أولاً! 👆');
                return;
            }

            const questionCard = document.querySelector(`[data-question="${questionId}"]`);
            const correctAnswer = correctAnswers[questionId];
            const userAnswer = selectedAnswers[questionId];
            const isCorrect = userAnswer === correctAnswer;

            // Mark question as answered
            answeredQuestions[questionId] = true;

            // Get all option buttons
            const optionButtons = questionCard.querySelectorAll('.option-btn');
            optionButtons.forEach(btn => {
                btn.classList.add('disabled');
                const answerText = btn.textContent.trim().charAt(0);
                if (answerText === correctAnswer) {
                    btn.classList.add('correct-answer');
                }
                if (btn.classList.contains('selected') && !isCorrect) {
                    btn.classList.add('wrong-answer');
                }
            });

            // Update question card styling
            if (isCorrect) {
                questionCard.classList.add('correct');
                gameState.diamonds += 10;
                gameState.coins += 25;
            } else {
                questionCard.classList.add('incorrect');
            }

            // Show explanation
            const explanationDiv = questionCard.querySelector('.answer-explanation');
            const explanationText = isCorrect ? explanations[questionId].correct : explanations[questionId].incorrect;
            explanationDiv.textContent = explanationText;
            explanationDiv.classList.add('show');

            // Hide verify button
            const verifyBtn = questionCard.querySelector('.verify-btn');
            verifyBtn.disabled = true;
            verifyBtn.textContent = isCorrect ? '✅ صحيح!' : '❌ خطأ!';

            updateResources();
        }

        function evaluateEssay() {
            const essay = document.getElementById('student-essay').value.trim();
            if (!essay) {
                alert('يرجى كتابة نصك أولاً!');
                return;
            }

            const wordCount = essay.split(/\s+/).length;
            const score = Math.min(100, Math.max(50, wordCount * 2));
            const diamonds = Math.floor(score / 10) * 15;
            const coins = Math.floor(score / 5) * 20;

            const evaluationHTML = `
                <h2>✅ تم التقييم بنجاح!</h2>
                <div class="reward">⭐ النتيجة: {{STAGE_NUM}}۰%</div>
                <div class="reward">💎 +{{DIAMONDS}} ماس</div>
                <div class="reward">🪙 +{{COINS}} عملات ذهبية</div>
                <p style="margin-top: 20px; color: var(--text-light);">أحسنت! 🎉 لقد أكملت جميع خطوات المرحلة {{STAGE_NUM}} بنجاح!</p>
            `;

            document.getElementById('evaluation-area').innerHTML = evaluationHTML
                .replace('{{STAGE_NUM}}۰%', score + '%')
                .replace('{{DIAMONDS}}', diamonds)
                .replace('{{COINS}}', coins)
                .replace('{{STAGE_NUM}}', 'المرحلة {{STAGE_NUM}}');

            document.getElementById('evaluation-area').style.display = 'block';

            gameState.diamonds += diamonds;
            gameState.coins += coins;
            updateResources();

            document.getElementById('nextBtn').textContent = '🏠 العودة للقائمة الرئيسية';
            document.getElementById('nextBtn').onclick = () => window.location.href = 'dashboard.html';
        }

        function updateResources() {
            document.getElementById('diamonds-count').textContent = gameState.diamonds;
            document.getElementById('coins-count').textContent = gameState.coins;
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateUI();
            updateResources();
        });
    </script>
</body>
</html>
'''

# Create files
base_path = r'c:\\xampppp\\htdocs\\monde-magique\\'

for stage in stages_data:
    content = template
    content = content.replace('{{TITLE}}', stage['title'])
    content = content.replace('{{SUBTITLE}}', stage['subtitle'])
    content = content.replace('{{FOLDER}}', stage['folder'])
    content = content.replace('{{PDF}}', stage['pdf'])
    
    file_path = os.path.join(base_path, stage['filename'])
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✓ Créé: {stage['filename']}")

print("\n🎉 Tous les stages 2-7 ont été créés avec succès!")
