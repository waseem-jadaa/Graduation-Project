<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FursaPal - اتصل بنا</title>
    <link rel="stylesheet" href="css/project.css" />
  </head>
  <body>
    <?php include 'header.php'; ?>
    
    <section class="contact-section">
      <div class="container">
        <div class="contact-container">
          <div class="contact-info">
            <h2>تواصل معنا</h2>
            <p>
              نحن هنا لمساعدتك! سواء كان لديك سؤال حول كيفية استخدام المنصة، أو
              تحتاج إلى دعم فني، أو ترغب في تقديم اقتراحات لتحسين الخدمة، لا
              تتردد في التواصل معنا.
            </p>

            <div class="contact-details">
              <div class="contact-item">
                <div class="contact-icon">📍</div>
                <div class="contact-text">
                  <h4>العنوان</h4>
                  <p>نابلس - شارع رفيديا</p>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-icon">📧</div>
                <div class="contact-text">
                  <h4>البريد الإلكتروني</h4>
                  <p>info@fursapal.ps</p>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-text">
                  <h4>الهاتف</h4>
                  <p>+970 59 123 4567</p>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-icon">⏱️</div>
                <div class="contact-text">
                  <h4>ساعات العمل</h4>
                  <p>من الأحد إلى الخميس<br />9:00 صباحاً - 5:00 مساءً</p>
                </div>
              </div>
            </div>
          </div>

          <div class="contact-form">
            <h2>أرسل رسالة</h2>
            <form>
              <div class="form-group">
                <label for="name">الاسم الكامل</label>
                <input type="text" id="name" required />
              </div>

              <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" required />
              </div>

              <div class="form-group">
                <label for="subject">الموضوع</label>
                <input type="text" id="subject" required />
              </div>

              <div class="form-group">
                <label for="message">الرسالة</label>
                <textarea id="message" required></textarea>
              </div>

              <button type="submit" class="submit-btn">إرسال الرسالة</button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="container">
        <div class="footer-grid">
          <div class="footer-about">
            <h3>عن فرصة بال</h3>
            <p>
              منصة توظيف إلكترونية تهدف إلى ربط المهنيين الفلسطينيين بفرص العمل
              المناسبة، والمساهمة في تخفيض معدلات البطالة وتحسين الظروف
              الاقتصادية.
            </p>
          </div>
          <div class="footer-links">
            <h3>روابط سريعة</h3>
            <ul>
              <li><a href="index.html">الرئيسية</a></li>
              <li><a href="#">الوظائف</a></li>
              <li><a href="#">المهنيون</a></li>
              <li><a href="about-us.html">من نحن</a></li>
            </ul>
          </div>
          <div class="footer-links">
            <h3>الدعم</h3>
            <ul>
              <li><a href="#">الأسئلة الشائعة</a></li>
              <li><a href="contact.html">اتصل بنا</a></li>
              <li><a href="#">الشروط والأحكام</a></li>
              <li><a href="#">سياسة الخصوصية</a></li>
            </ul>
          </div>
          <div class="footer-links">
            <h3>تواصل معنا</h3>
            <ul>
              <li><a href="#">فيسبوك</a></li>
              <li><a href="#">تويتر</a></li>
              <li><a href="#">انستغرام</a></li>
              <li><a href="#">لينكد إن</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>جميع الحقوق محفوظة &copy; 2025 فرصة بال</p>
        </div>
      </div>
    </footer>
  </body>
</html>
