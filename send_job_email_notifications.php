<?php
require_once 'db.php';

function sendJobEmailNotifications($job_id, $job_title, $job_location) {
    global $conn;
    error_log("Inside sendJobEmailNotifications function for Job ID: " . $job_id); // سجل لدخول الدالة
    
    try {
        // الحصول على جميع المهنيين الذين لديهم إشعارات البريد الإلكتروني مفعلة
        $stmt = $conn->prepare("
            SELECT u.email, u.name 
            FROM user u 
            WHERE u.email_notifications = TRUE 
            AND u.role = 'job_seeker'
        ");
        $stmt->execute();
        $professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Found " . count($professionals) . " professionals for email notification."); // سجل لعدد المهنيين

        if (empty($professionals)) {
            error_log("No professionals found with email notifications enabled and 'job_seeker' role.");
        }

        // إعداد محتوى البريد الإلكتروني
        $subject = "وظيفة جديدة: " . $job_title;
        $message = "
            <html dir='rtl'>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { padding: 20px; }
                    .job-title { color: #1abc5b; font-size: 18px; }
                    .job-details { margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>مرحباً {name}،</h2>
                    <p>تم نشر وظيفة جديدة قد تهمك:</p>
                    <div class='job-details'>
                        <p class='job-title'>{job_title}</p>
                        <p>الموقع: {job_location}</p>
                    </div>
                    <p>يمكنك الاطلاع على تفاصيل الوظيفة من خلال الرابط التالي:</p>
                    <p><a href='http://your-domain.com/jobs.php?id={job_id}'>عرض تفاصيل الوظيفة</a></p>
                </div>
            </body>
            </html>
        ";

        // إرسال البريد الإلكتروني لكل مهني
        foreach ($professionals as $professional) {
            $personalized_message = str_replace(
                ['{name}', '{job_title}', '{job_location}', '{job_id}'],
                [$professional['name'], $job_title, $job_location, $job_id],
                $message
            );

            $headers = "MIME-Version: 1.0" . "
";
            $headers .= "Content-type:text/html;charset=UTF-8" . "
";
            $headers .= 'From: Forsa Pal <noreply@forsapal.com>' . "
";

            // استخدم @mail لتجنب ظهور التحذيرات في حال فشل الإرسال (في بيئات التطوير)
            // في بيئة الإنتاج، يفضل معالجة الأخطاء بشكل أفضل
            @mail($professional['email'], $subject, $personalized_message, $headers);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error sending job email notifications: " . $e->getMessage());
        return false;
    }
}
?> 