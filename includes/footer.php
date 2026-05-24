</main>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand d-flex align-items-center gap-2 mb-3">
                    <div class="logo-icon"><i class="bi bi-compass-fill"></i></div>
                    <span class="brand-title">Учеба24</span>
                </div>
                <p class="footer-desc">Платформа для студентов. Библиотека, карта, отзывы и советы от выпускников.</p>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Разделы</h6>
                <ul class="footer-links">
                    <?php if ($user && ($isStudent || $isAdmin)): ?>
                    <li><a href="/student/library.php">Библиотека</a></li>
                    <li><a href="/student/map.php">Карта</a></li>
                    <li><a href="/student/teachers.php">Преподаватели</a></li>
                    <li><a href="/student/tips.php">Советы</a></li>
                    <?php endif; ?>
                    
                    <?php if ($isTeacher): ?>
                    <li><a href="/teacher/journal.php">Журнал</a></li>
                    <li><a href="/teacher/grades.php">Оценки</a></li>
                    <li><a href="/teacher/notifications.php">Рассылка</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Корпуса</h6>
                <ul class="footer-links">
                    <li><i class="bi bi-geo-alt-fill text-accent me-2"></i>ул. Брамса, 9</li>
                    <li><i class="bi bi-geo-alt-fill text-accent me-2"></i>ул. Спортивная, 6</li>
                    <li><i class="bi bi-geo-alt-fill text-accent me-2"></i>ул. Озерова, 7</li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Контакты</h6>
                <ul class="footer-links">
                    <li><i class="bi bi-envelope me-2"></i>support@studapp.ru</li>
                    <li><i class="bi bi-telephone me-2"></i>+7 (4012) 55-33-22</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Учеба24. Студенческий компас.</p>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/compass-main.js?v=5"></script>
</body>
</html>
