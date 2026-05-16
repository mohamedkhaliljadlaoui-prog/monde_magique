#!/usr/bin/env python3
# -*- coding: utf-8 -*-

stages_config = [
    {
        'num': 1,
        'filename': 'stage-1-tunisia.html',
        'icon': '🗺️',
        'title': 'المجال الجغرافي',
        'subtitle': 'اكتشف المفاهيم الأساسية للمجالات الجغرافية والطبيعية',
        'folder': 'f1',
        'pdf': 'cours1.pdf',
        'qcm': [
            {'q': 'ما هو المجال الجغرافي؟', 'a': 'أ) مساحة خاصة بالزراعة', 'b': 'ب) رقعة محددة لها حدود وخصائص', 'c': 'ج) مكان خالٍ من السكان', 'ans': 'ب'},
            {'q': 'ما الذي يشمله المجال الطبيعي؟', 'a': 'أ) المدارس', 'b': 'ب) الصخور والهواء والماء', 'c': 'ج) الطرقات', 'ans': 'ب'},
            {'q': 'ماذا يمثل المجال الجغرافي اجتماعياً؟', 'a': 'أ) التضاريس فقط', 'b': 'ب) العلاقات الاجتماعية والقيم', 'c': 'ج) المناخ', 'ans': 'ب'},
            {'q': 'ما هو المجال الجوي؟', 'a': 'أ) اليابسة', 'b': 'ب) الفضاء الجوي', 'c': 'ج) البحار', 'ans': 'ب'},
            {'q': 'ما هو المجال البحري؟', 'a': 'أ) الأراضي الزراعية', 'b': 'ب) الفضاء الجوي', 'c': 'ج) أجزاء البحار', 'ans': 'ج'},
        ]
    },
    {
        'num': 2,
        'filename': 'stage-2-maghreb.html',
        'icon': '🗺️',
        'title': 'المجال الجغرافي للمغرب العربي',
        'subtitle': 'اكتشف خصائص المجال الجغرافي للمغرب العربي',
        'folder': 'f2',
        'pdf': 'cours2.pdf',
        'qcm': [
            {'q': 'أين يقع المغرب العربي؟', 'a': 'أ) جنوب القارة الإفريقية', 'b': 'ب) شمال القارة الإفريقية', 'c': 'ج) في قارة آسيا', 'ans': 'ب'},
            {'q': 'ما هو أكبر بلد من حيث المساحة في المغرب العربي؟', 'a': 'أ) تونس', 'b': 'ب) ليبيا', 'c': 'ج) الجزائر', 'ans': 'ج'},
            {'q': 'كم عدد دول المغرب العربي؟', 'a': 'أ) 3 دول', 'b': 'ب) 4 دول', 'c': 'ج) 5 دول', 'ans': 'ج'},
            {'q': 'ماذا يغطي أغلب مساحة المغرب العربي؟', 'a': 'أ) الغابات', 'b': 'ب) البحار', 'c': 'ج) الصحراء', 'ans': 'ج'},
            {'q': 'كيف يكون المناخ في جنوب المغرب العربي؟', 'a': 'أ) رطب', 'b': 'ب) معتدل', 'c': 'ج) جاف', 'ans': 'ج'},
        ]
    },
    {
        'num': 3,
        'filename': 'stage-3-africa.html',
        'icon': '🌍',
        'title': 'المجال الجغرافي للبلاد التونسية',
        'subtitle': 'اكتشف التفاصيل الجغرافية لتونس',
        'folder': 'f3',
        'pdf': 'cours3.pdf',
        'qcm': [
            {'q': 'أين تقع تونس؟', 'a': 'أ) جنوب قارة إفريقيا', 'b': 'ب) شمال قارة إفريقيا', 'c': 'ج) في قارة آسيا', 'ans': 'ب'},
            {'q': 'ماذا يحدّ تونس من الشمال والشرق؟', 'a': 'أ) الصحراء', 'b': 'ب) البحر الأبيض المتوسط', 'c': 'ج) الجبال', 'ans': 'ب'},
            {'q': 'إلى كم منطقة ينقسم المجال الجغرافي لتونس؟', 'a': 'أ) منطقتين', 'b': 'ب) ثلاث مناطق', 'c': 'ج) أربع مناطق', 'ans': 'ب'},
            {'q': 'كيف يكون المناخ في الجنوب التونسي؟', 'a': 'أ) رطب', 'b': 'ب) معتدل', 'c': 'ج) صحراوي حار وجاف', 'ans': 'ج'},
            {'q': 'أيّ من الموارد التالية تُعدّ من الموارد الطبيعية في تونس؟', 'a': 'أ) السيارات', 'b': 'ب) الحبوب والزيتون والتمور', 'c': 'ج) المباني', 'ans': 'ب'},
        ]
    },
    {
        'num': 4,
        'filename': 'stage-4-europe.html',
        'icon': '🏘️',
        'title': 'المشهد الريفي المحلي',
        'subtitle': 'اكتشف خصائص المشهد الريفي والبيئة الريفية',
        'folder': 'f4',
        'pdf': 'cours2.pdf',
        'qcm': [
            {'q': 'ما المقصود بالمشهد الريفي؟', 'a': 'أ) صورة وقتية لمجال هيأه الإنسان لتعاطي الفلاحة', 'b': 'ب) مكان مخصص للصناعة فقط', 'c': 'ج) مجال طبيعي لا يتدخل فيه الإنسان', 'ans': 'أ'},
            {'q': 'ينقسم المشهد الريفي إلى نوعين هما:', 'a': 'أ) جبلي وساحلي', 'b': 'ب) مسيّج ومفتوح', 'c': 'ج) صناعي وتجاري', 'ans': 'ب'},
            {'q': 'ما الذي يميز المشهد الريفي المسيّج؟', 'a': 'أ) أراضيه غير محدودة', 'b': 'ب) أراضيه محدودة بسياج طبيعي أو اصطناعي', 'c': 'ج) يخلو من الأشجار', 'ans': 'ب'},
            {'q': 'أيّ من العناصر التالية يُعدّ من المجال الزراعي؟', 'a': 'أ) الجرارات', 'b': 'ب) الحقول والبساتين', 'c': 'ج) الطرقات', 'ans': 'ب'},
            {'q': 'من أنواع السكن الريفي:', 'a': 'أ) مساكن متفرقة ومساكن متجمعة', 'b': 'ب) عمارات شاهقة', 'c': 'ج) فنادق سياحية', 'ans': 'أ'},
        ]
    },
    {
        'num': 5,
        'filename': 'stage-5-asia.html',
        'icon': '🏙️',
        'title': 'المشهد الحضري',
        'subtitle': 'اكتشف خصائص المدن والمشاهد الحضرية',
        'folder': 'f5',
        'pdf': 'cours5.pdf',
        'qcm': [
            {'q': 'ما هو المشهد الحضري؟', 'a': 'أ) مجال طبيعي لا يتدخل فيه الإنسان', 'b': 'ب) صورة لمجال هيّأه الإنسان للعيش فيه', 'c': 'ج) مجال مخصّص للفلاحة فقط', 'ans': 'ب'},
            {'q': 'ماذا يميّز المباني في المدن؟', 'a': 'أ) البناء الأفقي فقط', 'b': 'ب) البناء العمودي', 'c': 'ج) غياب المباني', 'ans': 'ب'},
            {'q': 'كيف تكون المساكن في أطراف المدينة؟', 'a': 'أ) عمارات شاهقة', 'b': 'ب) مساكن فردية قليلة الارتفاع', 'c': 'ج) مصانع كبيرة', 'ans': 'ب'},
            {'q': 'ماذا يميّز المدينة؟', 'a': 'أ) قلة الطرقات', 'b': 'ب) غياب الخدمات', 'c': 'ج) كثرة الخدمات والمعالم والطرقات', 'ans': 'ج'},
            {'q': 'كيف تكون الكثافة السكانية في المجال الحضري؟', 'a': 'أ) ضعيفة', 'b': 'ب) متوسطة', 'c': 'ج) مرتفعة جدًا', 'ans': 'ج'},
        ]
    },
    {
        'num': 6,
        'filename': 'stage-6-namerica.html',
        'icon': '🔄',
        'title': 'العلاقة بين الريف و المدينة',
        'subtitle': 'اكتشف التفاعل بين المناطق الريفية والحضرية',
        'folder': 'f6',
        'pdf': 'cours6.pdf',
        'qcm': [
            {'q': 'ماذا يُميّز الريف؟', 'a': 'أ) كثافة سكانية مرتفعة', 'b': 'ب) قلة السكان واعتمادهم على الفلاحة', 'c': 'ج) كثرة المصانع', 'ans': 'ب'},
            {'q': 'ماذا تُميّز المدينة؟', 'a': 'أ) قلة المرافق', 'b': 'ب) الاعتماد على تربية الحيوانات فقط', 'c': 'ج) كثرة السكان وتنوّع الأنشطة', 'ans': 'ج'},
            {'q': 'ماذا يقدّم الريف للمدينة؟', 'a': 'أ) الملابس والآلات', 'b': 'ب) المواد الغذائية', 'c': 'ج) وسائل النقل', 'ans': 'ب'},
            {'q': 'كيف يتمّ الربط بين الريف والمدينة؟', 'a': 'أ) عبر الجبال فقط', 'b': 'ب) عبر البحار فقط', 'c': 'ج) عبر الطرقات ووسائل النقل', 'ans': 'ج'},
            {'q': 'ما أهمية العلاقة بين الريف والمدينة؟', 'a': 'أ) خلق التوازن وتلبية الحاجيات', 'b': 'ب) تقليل الإنتاج', 'c': 'ج) إضعاف الاقتصاد', 'ans': 'أ'},
        ]
    },
    {
        'num': 7,
        'filename': 'stage-7-samerica.html',
        'icon': '👥',
        'title': 'البلاد التونسية:التوزع الجغرافي للسكان',
        'subtitle': 'اكتشف توزيع السكان في تونس',
        'folder': 'f7',
        'pdf': 'cours7.pdf',
        'qcm': [
            {'q': 'أين تتركّز الكثافة السكانية المرتفعة جدّا في تونس؟', 'a': 'أ) الجنوب التونسي', 'b': 'ب) تونس الكبرى', 'c': 'ج) المناطق الصحراوية', 'ans': 'ب'},
            {'q': 'كيف تكون الكثافة السكانية في السواحل التونسية؟', 'a': 'أ) مرتفعة', 'b': 'ب) ضعيفة', 'c': 'ج) منعدمة', 'ans': 'أ'},
            {'q': 'ما سبب ضعف الكثافة السكانية في المناطق الداخلية؟', 'a': 'أ) كثرة الموانئ', 'b': 'ب) توفر الشغل', 'c': 'ج) صعوبة المناخ وقلّة الأمطار', 'ans': 'ج'},
            {'q': 'أين تتركّز الكثافة السكانية الضعيفة جدا؟', 'a': 'أ) الشمال الشرقي', 'b': 'ب) الوسط الشرقي', 'c': 'ج) الجنوب التونسي', 'ans': 'ج'},
            {'q': 'ما معنى "الإقليم" في هذا الدرس؟', 'a': 'أ) مدينة كبيرة', 'b': 'ب) جزء من التراب التونسي حسب الاتجاهات', 'c': 'ج) دولة مستقلة', 'ans': 'ب'},
        ]
    },
    {
        'num': 8,
        'filename': 'stage-8-oceania.html',
        'icon': '🌾',
        'title': 'التوزّع الفلاحي للبلاد التونسية',
        'subtitle': 'اكتشف التوزيع الجغرافي للنشاط الفلاحي',
        'folder': 'f8',
        'pdf': 'cours8.pdf',
        'qcm': [
            {'q': 'ماذا يُعتبر النشاط الفلاحي في تونس؟', 'a': 'أ) نشاط ثانوي', 'b': 'ب) نشاط أساسي', 'c': 'ج) نشاط محظور', 'ans': 'ب'},
            {'q': 'أين يتركّز الإنتاج الفلاحي الرئيسي؟', 'a': 'أ) الجنوب', 'b': 'ب) الشمال والوسط', 'c': 'ج) المناطق الساحلية فقط', 'ans': 'ب'},
            {'q': 'ما هي المحاصيل الرئيسية في تونس؟', 'a': 'أ) الكاكاو والبن', 'b': 'ب) الحبوب والزيتون والتمور', 'c': 'ج) الموز والأناناس', 'ans': 'ب'},
            {'q': 'ما أهمية الفلاحة في الاقتصاد التونسي؟', 'a': 'أ) غير مهمة', 'b': 'ب) أهمية كبيرة', 'c': 'ج) دور سلبي', 'ans': 'ب'},
            {'q': 'ماذا يحدّ النشاط الفلاحي في تونس؟', 'a': 'أ) توفر الماء المستمر', 'b': 'ب) المناخ الماطر دائماً', 'c': 'ج) ندرة الماء والمناخ الجاف', 'ans': 'ج'},
        ]
    },
    {
        'num': 9,
        'filename': 'stage-9-poles.html',
        'icon': '🏭',
        'title': 'الصناعة في البلاد التونسية',
        'subtitle': 'اكتشف أهمية الصناعة والتوزيع الصناعي في تونس',
        'folder': 'f9',
        'pdf': 'cours9.pdf',
        'qcm': [
            {'q': 'ماذا يميّز الموارد الطاقيّة والمنجميّة في تونس؟', 'a': 'أ) كثيرة جدًا ومتوفّرة بكميات هائلة', 'b': 'ب) قليلة وموزّعة على كامل البلاد', 'c': 'ج) موجودة فقط في الجنوب', 'ans': 'ب'},
            {'q': 'في أيّ جهة تكثر مناجم الفسفاط؟', 'a': 'أ) الشمال', 'b': 'ب) الشمال الشرقي', 'c': 'ج) الجنوب الغربي', 'ans': 'ج'},
            {'q': 'أين تتركّز أغلب الصناعات في تونس؟', 'a': 'أ) في المناطق الصحراوية', 'b': 'ب) في السواحل', 'c': 'ج) في الجبال', 'ans': 'ب'},
            {'q': 'أيّ من الصناعات التالية شهدت تطوّرًا في تونس؟', 'a': 'أ) صناعة الأجهزة المرئية', 'b': 'ب) صناعة النسيج', 'c': 'ج) صناعة الفولاذ', 'ans': 'ب'},
            {'q': 'لماذا يجب ترشيد استهلاك الموارد في تونس؟', 'a': 'أ) لأنها غير مهمّة', 'b': 'ب) لأنها متوفّرة بكثرة', 'c': 'ج) لأنها قليلة وبعضها في طور النضوب', 'ans': 'ج'},
        ]
    },
    {
        'num': 10,
        'filename': 'stage-10-world.html',
        'icon': '🤝',
        'title': 'التجارة الخارجية التونسية',
        'subtitle': 'اكتشف الصادرات والواردات والميزان التجاري',
        'folder': 'f10',
        'pdf': 'cours10.pdf',
        'qcm': [
            {'q': 'على ماذا تعتمد الصادرات التونسية أساسًا؟', 'a': 'أ) المنتجات الفلاحية', 'b': 'ب) المنتجات الصناعية', 'c': 'ج) المواد الخشبية', 'ans': 'ب'},
            {'q': 'نحو أيّ جهة تتجه أغلب الصادرات التونسية؟', 'a': 'أ) إفريقيا', 'b': 'ب) أوروبا', 'c': 'ج) أمريكا', 'ans': 'ب'},
            {'q': 'ممّ تتكوّن الواردات التونسية؟', 'a': 'أ) مواد صناعية وغذائية وطاقية', 'b': 'ب) منتجات سياحية فقط', 'c': 'ج) مواد فلاحية فقط', 'ans': 'أ'},
            {'q': 'ماذا تعاني منه تونس في تجارتها الخارجية؟', 'a': 'أ) فائض تجاري كبير', 'b': 'ب) توازن تجاري دائم', 'c': 'ج) عجز تجاري متزايد', 'ans': 'ج'},
            {'q': 'ما هو أهمّ شريك تجاري لتونس؟', 'a': 'أ) الاتحاد الأوروبي', 'b': 'ب) دول آسيا', 'c': 'ج) دول أمريكا', 'ans': 'أ'},
        ]
    }
]

template = '''<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ICON}} {{TITLE}} - Stage {{NUM}}/10</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--candy-red:#E91E63;--candy-orange:#FF9800;--candy-yellow:#FFD54F;--candy-green:#26C281;--candy-teal:#00BCD4;--candy-blue:#2196F3;--candy-purple:#9C27B0;--candy-pink:#EC407A;--bg-deep:#0D0221;--bg-mid:#1A0033;--bg-card:#2B0A54;--text-light:#FFFFFF;--text-muted:rgba(255,255,255,0.65)}
        *{margin:0;padding:0;box-sizing:border-box}html,body{height:100%;font-family:'Nunito',sans-serif;background:var(--bg-deep);color:var(--text-light);overflow-x:hidden}
        body::before{content:'';position:fixed;inset:0;background:radial-gradient(circle at 15% 20%,#FF336622 0%,transparent 40%),radial-gradient(circle at 85% 70%,#00AAFF22 0%,transparent 40%);pointer-events:none;z-index:0}
        .stage-container{max-width:1200px;margin:0 auto;padding:40px 20px;position:relative;z-index:1}
        .stage-header{text-align:center;margin-bottom:50px;text-shadow:0 2px 8px rgba(0,0,0,0.3)}
        .stage-header h1{font-size:2.8rem;color:var(--candy-yellow);margin-bottom:15px}
        .stage-header p{font-size:1.2rem;opacity:0.9;color:var(--text-muted)}
        .stage-num{font-size:0.9rem;color:var(--candy-teal);font-weight:700;letter-spacing:2px}
        .resources{display:flex;justify-content:center;gap:20px;margin-bottom:30px;flex-wrap:wrap}
        .resource-display{background:linear-gradient(135deg,var(--bg-card) 0%,var(--bg-mid) 100%);padding:15px 25px;border-radius:15px;border:2px solid var(--candy-yellow);font-weight:700;display:flex;align-items:center;gap:10px;font-size:1.3rem;color:var(--candy-yellow);box-shadow:0 0 20px rgba(255,215,0,0.3);animation:scaleIn 0.4s ease-out;transition:all 0.3s}
        .resource-display:hover{transform:scale(1.05);box-shadow:0 0 30px rgba(255,215,0,0.5)}
        .steps-progress{display:flex;justify-content:space-around;margin:40px 0;flex-wrap:wrap;gap:15px;position:relative}
        .steps-progress::before{content:'';position:absolute;top:40px;left:15%;right:15%;height:3px;background:linear-gradient(to right,var(--candy-purple),var(--candy-blue),var(--candy-teal));z-index:0}
        .step{flex:1;min-width:130px;text-align:center;cursor:pointer;transition:all 0.3s;position:relative;z-index:1}
        .step-icon{width:80px;height:80px;background:linear-gradient(135deg,var(--bg-card) 0%,var(--bg-mid) 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 10px;border:3px solid var(--candy-purple);transition:all 0.3s;box-shadow:0 4px 20px rgba(156,39,176,0.3)}
        .step.active .step-icon{background:linear-gradient(135deg,var(--candy-purple) 0%,#E91E63 100%);border-color:var(--candy-yellow);transform:scale(1.15);box-shadow:0 0 30px rgba(156,39,176,0.6)}
        .step.completed .step-icon{background:linear-gradient(135deg,var(--candy-green) 0%,#26C281 100%);border-color:var(--candy-green);font-size:3rem}
        .step.locked .step-icon{opacity:0.4;cursor:not-allowed;border-color:var(--candy-red)}
        .step-label{font-weight:700;color:var(--text-muted);font-size:0.95rem;margin-top:8px}
        .step.active .step-label,.step.completed .step-label{color:var(--candy-yellow)}
        .stage-content{background:linear-gradient(135deg,var(--bg-card) 0%,var(--bg-mid) 100%);border-radius:20px;padding:50px;box-shadow:0 10px 50px rgba(156,39,176,0.3);border:2px solid rgba(156,39,176,0.2);min-height:500px;animation:glow 2s ease-in-out infinite}
        @keyframes glow{0%,100%{box-shadow:0 10px 50px rgba(156,39,176,0.3),inset 0 0 40px rgba(156,39,176,0.1)}50%{box-shadow:0 10px 60px rgba(156,39,176,0.5),inset 0 0 40px rgba(156,39,176,0.15)}}
        @keyframes slideIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}@keyframes scaleIn{from{opacity:0;transform:scale(0.8)}to{opacity:1;transform:scale(1)}}@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}50%{transform:translateX(5px)}75%{transform:translateX(-5px)}}@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.7}}@keyframes slideRight{from{opacity:0;transform:translateX(-30px)}to{opacity:1;transform:translateX(0)}}@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .step-content{display:none;animation:slideIn 0.6s ease-out}.step-content.active{display:block}.step-content h2{font-size:2rem;color:var(--candy-yellow);margin-bottom:25px;text-shadow:0 2px 10px rgba(255,215,0,0.3)}
        .image-container,.pdf-container{width:100%;background:#000;border-radius:15px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.4);margin-bottom:25px;border:3px solid var(--candy-blue);display:flex;align-items:center;justify-content:center;animation:slideIn 0.6s ease-out}
        .image-container{max-height:600px}.pdf-container{height:800px}
        .image-container img,.pdf-viewer{width:100%;height:100%;object-fit:contain;animation:scaleIn 0.7s ease-out}
        .text-content{font-size:1.1rem;line-height:1.8;color:var(--text-light);margin-bottom:20px;animation:slideRight 0.5s ease-out}.text-content h3{color:var(--candy-yellow);font-size:1.4rem;margin:20px 0 10px}
        .qcm-container{display:flex;flex-direction:column;gap:25px}.question-card{background:rgba(156,39,176,0.1);border-left:5px solid var(--candy-purple);padding:20px;border-radius:10px;border:2px solid rgba(156,39,176,0.3);animation:slideRight 0.5s ease-out;transition:all 0.3s}
        .question-card:hover{border:2px solid var(--candy-purple);box-shadow:0 4px 20px rgba(156,39,176,0.3)}.question-card.correct{background:rgba(38,194,129,0.1);border-left:5px solid var(--candy-green);border:2px solid var(--candy-green);animation:pulse 0.5s ease-out}.question-card.incorrect{background:rgba(233,30,99,0.1);border-left:5px solid var(--candy-red);border:2px solid var(--candy-red);animation:shake 0.5s ease-out}
        .question-card h3{color:var(--candy-yellow);margin-bottom:15px;font-size:1.1rem}.options{display:flex;flex-direction:column;gap:10px}
        .option-btn{background:rgba(255,215,0,0.05);border:2px solid rgba(255,215,0,0.3);color:var(--text-light);padding:12px 15px;border-radius:8px;cursor:pointer;transition:all 0.3s;font-size:1rem;text-align:right;animation:slideRight 0.4s ease-out}
        .option-btn:hover{background:rgba(255,215,0,0.15);border-color:var(--candy-yellow);transform:translateX(-5px)}.option-btn.selected{background:var(--candy-yellow);color:var(--bg-deep);border-color:var(--candy-yellow);font-weight:700;transform:scale(1.02)}.option-btn.disabled{cursor:not-allowed;opacity:0.6}
        .option-btn.correct-answer{background:var(--candy-green)!important;color:white!important;border-color:var(--candy-green)!important;animation:pulse 0.5s ease-out}.option-btn.wrong-answer{background:var(--candy-red)!important;color:white!important;border-color:var(--candy-red)!important;animation:shake 0.3s ease-out}
        .answer-explanation{color:var(--candy-green);margin-top:15px;font-size:0.95rem;font-weight:600;display:none;animation:slideRight 0.5s ease-out}.answer-explanation.show{display:block}
        .warning-message{background:rgba(233,30,99,0.15);border-left:4px solid var(--candy-red);padding:15px;border-radius:8px;margin-top:15px;color:var(--candy-red);display:none;font-weight:700}
        .warning-message.show{display:block}
        .verify-btn{background:linear-gradient(135deg,var(--candy-blue) 0%,var(--candy-teal) 100%);color:var(--text-light);border:none;padding:10px 25px;border-radius:25px;font-size:0.95rem;font-weight:700;cursor:pointer;margin-top:10px;transition:all 0.3s;display:inline-block;animation:slideRight 0.5s ease-out}
        .verify-btn:hover:not(:disabled){transform:translateY(-3px);box-shadow:0 4px 15px rgba(0,188,212,0.4)}.verify-btn:disabled{opacity:0.5;cursor:not-allowed}
        textarea{width:100%;height:300px;padding:20px;border-radius:10px;border:2px solid var(--candy-purple);background:rgba(255,255,255,0.05);color:var(--text-light);font-size:1rem;font-family:inherit;resize:vertical;animation:slideIn 0.5s ease-out;transition:all 0.3s}
        textarea:focus{border-color:var(--candy-blue);box-shadow:0 0 15px rgba(33,150,243,0.3);outline:none}
        .step-controls{display:flex;gap:15px;margin-top:40px;justify-content:center;flex-wrap:wrap;animation:slideUp 0.5s ease-out}
        .btn{background:linear-gradient(135deg,var(--candy-purple) 0%,var(--candy-pink) 100%);color:var(--text-light);border:none;padding:15px 40px;border-radius:50px;font-size:1.1rem;font-weight:700;cursor:pointer;transition:all 0.3s;box-shadow:0 4px 15px rgba(156,39,176,0.4);display:flex;align-items:center;gap:10px;justify-content:center;animation:scaleIn 0.4s ease-out}
        .btn:hover{transform:translateY(-3px) translateX(0);box-shadow:0 6px 20px rgba(156,39,176,0.6)}.btn:active{transform:translateY(-1px)}.btn:disabled{opacity:0.5;cursor:not-allowed}.btn-prev{background:linear-gradient(135deg,var(--candy-blue) 0%,var(--candy-teal) 100%)}
        #evaluation-area{margin-top:30px;padding:25px;background:linear-gradient(135deg,rgba(38,194,129,0.2) 0%,rgba(76,175,80,0.1) 100%);border-radius:12px;border:2px solid var(--candy-green);text-align:center;animation:slideUp 0.5s ease-out}
        #evaluation-area h2{color:var(--candy-green);margin-bottom:15px;animation:scaleIn 0.4s ease-out}#evaluation-area .reward{font-size:1.5rem;margin:10px 0;color:var(--candy-yellow);animation:slideRight 0.5s ease-out}
        .qcm-score{background:rgba(0,188,212,0.1);border-left:4px solid var(--candy-teal);padding:15px;border-radius:8px;margin-top:20px;text-align:center;color:var(--text-light)}
        .qcm-score strong{color:var(--candy-yellow);font-size:1.2rem}
        @media(max-width:768px){.stage-header h1{font-size:1.8rem}.step-icon{width:60px;height:60px;font-size:1.8rem}.stage-content{padding:25px}.steps-progress{gap:10px}.step{min-width:100px}.pdf-container{height:600px}}
    </style>
</head>
<body>
    <div class="stage-container">
        <div class="stage-header">
            <div class="stage-num">STAGE {{NUM}}/10</div>
            <h1>{{ICON}} {{TITLE}}</h1>
            <p>{{SUBTITLE}}</p>
        </div>
        <div class="resources">
            <div class="resource-display"><span>💎</span><span id="diamonds-count">150</span></div>
            <div class="resource-display"><span>🪙</span><span id="coins-count">750</span></div>
        </div>
        <div class="steps-progress">
            <div class="step active" onclick="goToStep(1)"><div class="step-icon">🖼️</div><div class="step-label">البداية</div></div>
            <div class="step" onclick="goToStep(2)"><div class="step-icon">🎨</div><div class="step-label">الصورة 1</div></div>
            <div class="step" onclick="goToStep(3)"><div class="step-icon">📸</div><div class="step-label">الصورة 2</div></div>
            <div class="step" onclick="goToStep(4)"><div class="step-icon">📚</div><div class="step-label">الدرس</div></div>
            <div class="step" onclick="goToStep(5)"><div class="step-icon">❓</div><div class="step-label">اختبار</div></div>
            <div class="step" onclick="goToStep(6)"><div class="step-icon">✍️</div><div class="step-label">الكتابة</div></div>
        </div>
        <div class="stage-content">
            <div class="step-content active">
                <h2>🖼️ الخطوة 1: ابدأ رحلتك</h2>
                <div class="image-container"><img src="assets/stage/{{FOLDER}}/debut.png" alt="صورة البداية"></div>
                <div class="text-content"><h3>مرحباً بك!</h3><p>هذه الصورة تمثل بداية رحلتك في فهم {{TITLE}}. ستتعلم في هذه المرحلة المفاهيم الأساسية.</p></div>
            </div>
            <div class="step-content">
                <h2>🎨 الخطوة 2: الصور التوضيحية</h2>
                <div class="image-container"><img src="assets/stage/{{FOLDER}}/v.jpg" alt="صورة توضيحية"></div>
                <div class="text-content"><h3>فهم أعمق للموضوع</h3><p>هذه الصورة تقدم لك رؤية مفصلة. لاحظ العناصر المختلفة.</p></div>
            </div>
            <div class="step-content">
                <h2>📸 الخطوة 3: معلومات متقدمة</h2>
                <div class="image-container"><img src="assets/stage/{{FOLDER}}/p.png" alt="صورة إضافية"></div>
                <div class="text-content"><h3>تفاصيل إضافية مهمة</h3><p>هذه الصورة توفر معلومات إضافية وتفاصيل متقدمة.</p></div>
            </div>
            <div class="step-content">
                <h2>📚 الخطوة 4: الدرس الكامل</h2>
                <div class="pdf-container"><iframe class="pdf-viewer" src="assets/stage/{{FOLDER}}/{{PDF}}"></iframe></div>
                <div style="margin-top:20px;padding:15px;background:rgba(33,150,243,0.1);border-radius:10px;border-left:4px solid var(--candy-blue)"><p style="color:var(--text-light)">📖 اقرأ الدرس بعناية قبل الاختبار.</p></div>
            </div>
            <div class="step-content">
                <h2>❓ الخطوة 5: اختبر معلوماتك (80% مطلوب)</h2>
                <div class="qcm-container">
{{QCM_HTML}}
                </div>
                <div class="qcm-score" id="qcm-score" style="display:none"></div>
                <div class="warning-message" id="qcm-warning">⚠️ يجب تحقيق 80% على الأقل للمتابعة! أعد محاولتك.</div>
            </div>
            <div class="step-content">
                <h2>✍️ الخطوة 6: اكتب فقرتك</h2>
                <p style="color:var(--text-muted);margin-bottom:20px;font-size:1.05rem">📝 اكتب فقرة (100-200 كلمة):</p>
                <textarea id="student-essay" placeholder="اكتب هنا..."></textarea>
                <div style="margin-top:20px;padding:15px;background:rgba(156,39,176,0.1);border-radius:10px;border-left:4px solid var(--candy-purple)"><p style="color:var(--text-light)">💡استخدم جملاً واضحة.</p></div>
                <div id="evaluation-area" style="display:none"></div>
            </div>
        </div>
        <div class="step-controls">
            <button class="btn btn-prev" onclick="previousStep()" style="display:none" id="prevBtn">⬅️ السابق</button>
            <button class="btn" onclick="nextStep()" id="nextBtn">التالي ➡️</button>
        </div>
    </div>
    <script>
        let currentStep=1;const totalSteps=6;const selectedAnswers={};const answeredQuestions={{QCM_DATA}};const correctAnswers={{QCM_ANSWERS}};const gameState={diamonds:150,coins:750,qcmScore:0,stageNum:{{NUM}}};
        
        function goToStep(s){if(s>=1&&s<=totalSteps){if(s===6&&currentStep===5&&gameState.qcmScore<80){alert('يجب تحقيق 80% في الاختبار أولاً!');return}currentStep=s;updateUI()}}
        
        function nextStep(){if(currentStep===5){const correct=Object.values(answeredQuestions).filter(x=>x===1).length;const score=Math.round(correct/5*100);gameState.qcmScore=score;document.getElementById('qcm-score').innerHTML=`<strong>النتيجة: ${score}%</strong>`;document.getElementById('qcm-score').style.display='block';if(score<80){document.getElementById('qcm-warning').classList.add('show');return}else{document.getElementById('qcm-warning').classList.remove('show')}}if(currentStep<totalSteps){currentStep++;updateUI()}else if(currentStep===totalSteps){evaluateEssay()}}
        
        function previousStep(){if(currentStep>1){currentStep--;updateUI()}}
        
        function updateUI(){document.querySelectorAll('.step-content').forEach((e,i)=>{e.classList.toggle('active',i+1===currentStep)});document.querySelectorAll('.step').forEach((e,i)=>{if(i+1===currentStep)e.classList.add('active');else e.classList.remove('active')});document.getElementById('prevBtn').style.display=currentStep>1?'flex':'none';document.getElementById('nextBtn').textContent=currentStep===totalSteps?'✅تقييم':'التالي ➡️';window.scrollTo({top:0,behavior:'smooth'})}
        
        function selectOption(b,q){b.parentElement.querySelectorAll('.option-btn').forEach(x=>{x.classList.remove('selected')});b.classList.add('selected');selectedAnswers[q]=b.textContent.trim().charAt(0)}
        
        function verifyAnswer(q){if(!selectedAnswers[q]){alert('اختر إجابة!');return}const c=document.querySelector(`[data-question="${q}"]`);const ans=correctAnswers[q];const u=selectedAnswers[q];const ok=u===ans;answeredQuestions[q]=ok?1:0;c.querySelectorAll('.option-btn').forEach(b=>{b.classList.add('disabled');if(b.textContent.trim().charAt(0)===ans)b.classList.add('correct-answer');if(b.classList.contains('selected')&&!ok)b.classList.add('wrong-answer')});if(ok){c.classList.add('correct');gameState.diamonds+=10;gameState.coins+=25}else{c.classList.add('incorrect')}const ex=c.querySelector('.answer-explanation');ex.textContent=ok?'✓أحسنت!':'✗للأسف.';ex.classList.add('show');c.querySelector('.verify-btn').disabled=true;c.querySelector('.verify-btn').textContent=ok?'✅صحيح':'❌خطأ';updateResources()}
        
        function evaluateEssay(){const e=document.getElementById('student-essay').value.trim();if(!e){alert('اكتب فقرة!');return}const w=e.split(/\\s+/).length;const s=Math.min(100,Math.max(50,w*2));const d=Math.floor(s/10)*15;const coins=Math.floor(s/5)*20;let nextBtn='🏠العودة';if(gameState.stageNum<10){nextBtn='➡️ المرحلة التالية'}const h=`<h2>✅تم!</h2><div class="reward">⭐${s}%</div><div class="reward">💎+${d}</div><div class="reward">🪙+${coins}</div><p style="margin-top:20px;color:var(--text-light)">احسنت!</p>`;document.getElementById('evaluation-area').innerHTML=h;document.getElementById('evaluation-area').style.display='block';gameState.diamonds+=d;gameState.coins+=coins;updateResources();document.getElementById('nextBtn').textContent=nextBtn;document.getElementById('nextBtn').onclick=()=>{if(gameState.stageNum<10){window.location.href=`stage-${gameState.stageNum+1}-*.html`.replace('*',['tunisia','maghreb','africa','europe','asia','namerica','samerica','oceania','poles','world'][gameState.stageNum])}else{window.location.href='dashboard.html'}}}
        
        function updateResources(){document.getElementById('diamonds-count').textContent=gameState.diamonds;document.getElementById('coins-count').textContent=gameState.coins}
        
        document.addEventListener('DOMContentLoaded',()=>{updateUI();updateResources()});
    </script>
</body>
</html>'''

def generate_qcm_html(qcm_list):
    html = ''
    for i, q in enumerate(qcm_list, 1):
        qid = f'q{i}'
        ans_char = q['ans'][0]
        html += f'''                    <div class="question-card" data-question="{qid}" data-answer="{ans_char}"><h3>السؤال {i}: {q['q']}</h3><div class="options"><button class="option-btn" onclick="selectOption(this,'{qid}')">{q['a']}</button><button class="option-btn" onclick="selectOption(this,'{qid}')">{q['b']}</button><button class="option-btn" onclick="selectOption(this,'{qid}')">{q['c']}</button></div><button class="verify-btn" onclick="verifyAnswer('{qid}')">✓ تحقق</button><div class="answer-explanation"></div></div>
'''
    return html

def generate_qcm_data(qcm_list):
    return '{' + ','.join([f'q{i}:0' for i in range(1, len(qcm_list)+1)]) + '}'

def generate_qcm_answers(qcm_list):
    return '{' + ','.join([f'q{i}:\'{q["ans"][0]}\'' for i, q in enumerate(qcm_list, 1)]) + '}'

base_path = r'c:\xampppp\htdocs\monde-magique\\'

for stage in stages_config:
    content = template
    content = content.replace('{{NUM}}', str(stage['num']))
    content = content.replace('{{ICON}}', stage['icon'])
    content = content.replace('{{TITLE}}', stage['title'])
    content = content.replace('{{SUBTITLE}}', stage['subtitle'])
    content = content.replace('{{FOLDER}}', stage['folder'])
    content = content.replace('{{PDF}}', stage['pdf'])
    content = content.replace('{{QCM_HTML}}', generate_qcm_html(stage['qcm']))
    content = content.replace('{{QCM_DATA}}', generate_qcm_data(stage['qcm']))
    content = content.replace('{{QCM_ANSWERS}}', generate_qcm_answers(stage['qcm']))
    
    file_path = base_path + stage['filename']
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✓ Stage {stage['num']:2d}: {stage['title'][:40]}")

print(f"\n✅ All 10 stages updated with specific QCM questions!")
