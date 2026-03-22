// js/grades.js - Расширенный функционал для страницы успеваемости

class GradesManager {
    constructor() {
        this.gradesData = this.loadGradesData();
        this.currentSemester = 5;
        this.init();
    }

    // Загрузка данных из localStorage
    loadGradesData() {
        const saved = localStorage.getItem('studapp_grades');
        if (saved) {
            return JSON.parse(saved);
        }
        
        // Данные по умолчанию
        return this.getDefaultGradesData();
    }

    // Данные по умолчанию
    getDefaultGradesData() {
        return {
            student: {
                name: "Иван Иванов",
                group: "ИТ-321",
                course: 3
            },
            semesters: {
                5: {
                    gpa: 4.6,
                    attendance: 85,
                    subjects: [
                        {
                            id: 1,
                            name: "Программирование",
                            teacher: "Петров И.С.",
                            credits: 4,
                            grade: 5,
                            progress: 100,
                            gradesHistory: [
                                { date: "15.03.2024", type: "Лабораторная №5", grade: 5 },
                                { date: "10.03.2024", type: "Лабораторная №4", grade: 5 },
                                { date: "05.03.2024", type: "Контрольная работа", grade: 5 }
                            ]
                        },
                        {
                            id: 2,
                            name: "Математический анализ",
                            teacher: "Лесная М.А.",
                            credits: 5,
                            grade: 5,
                            progress: 100,
                            gradesHistory: [
                                { date: "12.03.2024", type: "Контрольная работа", grade: 5 },
                                { date: "01.03.2024", type: "Тест", grade: 5 }
                            ]
                        },
                        {
                            id: 3,
                            name: "Физика",
                            teacher: "Сидоров А.В.",
                            credits: 4,
                            grade: 3,
                            progress: 60,
                            gradesHistory: [
                                { date: "10.03.2024", type: "Тест по оптике", grade: 3 },
                                { date: "05.03.2024", type: "Лабораторная №2", grade: 4 }
                            ]
                        }
                    ]
                },
                4: {
                    gpa: 4.4,
                    attendance: 82,
                    subjects: [
                        // Данные для 4 семестра
                    ]
                }
            },
            semesterStats: {
                5: { excellent: 8, good: 4, satisfactory: 2, poor: 0 },
                4: { excellent: 6, good: 6, satisfactory: 2, poor: 0 },
                3: { excellent: 5, good: 5, satisfactory: 3, poor: 1 },
                2: { excellent: 4, good: 6, satisfactory: 3, poor: 1 },
                1: { excellent: 3, good: 5, satisfactory: 4, poor: 2 }
            }
        };
    }

    init() {
        this.renderCurrentSemester();
        this.setupEventListeners();
        this.updateCharts();
    }

    // Рендеринг текущего семестра
    renderCurrentSemester() {
        const semesterData = this.gradesData.semesters[this.currentSemester];
        if (!semesterData) return;

        // Обновление статистики
        this.updateStatistics(semesterData);
        
        // Обновление списка предметов
        this.renderSubjects(semesterData.subjects);
    }

    // Обновление статистики
    updateStatistics(semesterData) {
        document.querySelectorAll('.stat-value')[0].textContent = semesterData.gpa.toFixed(1);
        document.querySelectorAll('.stat-value')[3].textContent = semesterData.attendance + '%';
        
        const passedSubjects = semesterData.subjects.filter(s => s.progress === 100).length;
        const atRiskSubjects = semesterData.subjects.filter(s => s.grade < 4 && s.progress < 100).length;
        
        document.querySelectorAll('.stat-value')[1].textContent = passedSubjects;
        document.querySelectorAll('.stat-value')[2].textContent = atRiskSubjects;
    }

    // Рендеринг предметов
    renderSubjects(subjects) {
        const container = document.querySelector('.card-body');
        if (!container) return;

        container.innerHTML = '';

        subjects.forEach(subject => {
            const subjectElement = this.createSubjectElement(subject);
            container.appendChild(subjectElement);
        });
    }

    // Создание элемента предмета
    createSubjectElement(subject) {
        const element = document.createElement('div');
        element.className = 'grade-card p-4 mb-3 fade-in-up';
        element.dataset.subject = subject.name.toLowerCase().replace(/\s+/g, '-');
        element.dataset.grade = subject.grade;
        element.dataset.id = subject.id;

        const gradeClass = this.getGradeClass(subject.grade);
        const gradeText = this.getGradeText(subject.grade);

        element.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi ${this.getSubjectIcon(subject.name)} fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">${subject.name}</h6>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-person me-1"></i>
                                ${subject.teacher}
                                <span class="mx-2">•</span>
                                <i class="bi bi-clock me-1"></i>
                                ${subject.credits} кредита
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="mb-1">
                                <span class="grade-badge ${gradeClass}">${gradeText}</span>
                            </div>
                            <div class="grade-progress">
                                <div class="grade-progress-bar ${this.getProgressColor(subject.progress)}" 
                                     style="width: ${subject.progress}%"></div>
                            </div>
                            <small class="text-muted">Прогресс: ${subject.progress}%</small>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-primary view-details-btn">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Добавляем обработчик для кнопки деталей
        const detailsBtn = element.querySelector('.view-details-btn');
        detailsBtn.addEventListener('click', () => this.showSubjectDetails(subject));

        return element;
    }

    // Получение класса для оценки
    getGradeClass(grade) {
        if (grade >= 4.5) return 'grade-excellent';
        if (grade >= 3.5) return 'grade-good';
        if (grade >= 2.5) return 'grade-satisfactory';
        return 'grade-poor';
    }

    // Получение текста оценки
    getGradeText(grade) {
        if (grade >= 4.5) return `Отлично (${grade})`;
        if (grade >= 3.5) return `Хорошо (${grade})`;
        if (grade >= 2.5) return `Удовл. (${grade})`;
        return `Неудовл. (${grade})`;
    }

    // Получение иконки предмета
    getSubjectIcon(subjectName) {
        const icons = {
            'Программирование': 'bi-code-slash',
            'Математический анализ': 'bi-calculator',
            'Физика': 'bi-lightning-charge',
            'Английский язык': 'bi-translate',
            'История': 'bi-book',
            'Базы данных': 'bi-database',
            'Web-разработка': 'bi-globe'
        };
        
        for (const [key, icon] of Object.entries(icons)) {
            if (subjectName.includes(key)) return icon;
        }
        
        return 'bi-book';
    }

    // Получение цвета прогресса
    getProgressColor(progress) {
        if (progress >= 90) return 'bg-success';
        if (progress >= 70) return 'bg-info';
        if (progress >= 50) return 'bg-warning';
        return 'bg-danger';
    }

    // Показать детали предмета
    showSubjectDetails(subject) {
        const modal = new bootstrap.Modal(document.getElementById('subjectDetailsModal'));
        
        // Обновляем содержимое модального окна
        const modalTitle = document.querySelector('#subjectDetailsModalLabel');
        const modalBody = document.querySelector('#subjectDetailsModal .modal-body');
        
        modalTitle.innerHTML = `<i class="bi bi-info-circle me-2"></i>${subject.name}`;
        
        let historyHTML = '';
        subject.gradesHistory.forEach(item => {
            historyHTML += `
                <div class="d-flex justify-content-between mb-1">
                    <span>${item.type}</span>
                    <span class="badge ${this.getGradeBadgeClass(item.grade)}">${item.grade}</span>
                </div>
            `;
        });
        
        modalBody.innerHTML = `
            <p class="text-muted">Преподаватель: ${subject.teacher}</p>
            <div class="mb-3">
                <strong>Все оценки:</strong>
                <div class="mt-2">
                    ${historyHTML}
                </div>
            </div>
            <div class="alert ${subject.grade >= 4.5 ? 'alert-success' : 'alert-warning'}">
                <i class="bi ${subject.grade >= 4.5 ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
                ${subject.grade >= 4.5 ? 'Предмет успешно сдан.' : 'Требует дополнительного внимания.'}
                Средний балл: ${subject.grade.toFixed(1)}
            </div>
        `;
        
        modal.show();
    }

    // Получение класса бейджа оценки
    getGradeBadgeClass(grade) {
        if (grade >= 4.5) return 'bg-success';
        if (grade >= 3.5) return 'bg-primary';
        if (grade >= 2.5) return 'bg-warning';
        return 'bg-danger';
    }

    // Настройка обработчиков событий
    setupEventListeners() {
        // Переключение семестров
        document.querySelectorAll('.semester-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.currentSemester = parseInt(btn.dataset.semester);
                this.renderCurrentSemester();
                this.updateCharts();
                
                document.querySelectorAll('.semester-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                StudApp.showNotification(`Загружен ${this.currentSemester} семестр`, 'info');
            });
        });

        // Экспорт данных
        document.getElementById('exportGradesBtn').addEventListener('click', () => {
            this.exportGrades();
        });

        // Сортировка
        document.getElementById('sortByGrade').addEventListener('click', () => {
            this.sortSubjectsByGrade();
        });

        document.getElementById('sortBySubject').addEventListener('click', () => {
            this.sortSubjectsByName();
        });
    }

    // Обновление графиков
    updateCharts() {
        if (window.gradesChart) {
            const semesterStats = this.gradesData.semesterStats;
            
            // Обновляем линейный график
            window.gradesChart.data.datasets[0].data = [
                this.gradesData.semesters[1]?.gpa || 3.8,
                this.gradesData.semesters[2]?.gpa || 4.0,
                this.gradesData.semesters[3]?.gpa || 4.2,
                this.gradesData.semesters[4]?.gpa || 4.4,
                this.gradesData.semesters[5]?.gpa || 4.6
            ];
            window.gradesChart.update();
            
            // Обновляем круговую диаграмму
            const currentStats = semesterStats[this.currentSemester] || semesterStats[5];
            window.distributionChart.data.datasets[0].data = [
                currentStats.excellent,
                currentStats.good,
                currentStats.satisfactory
            ];
            window.distributionChart.update();
        }
    }

    // Сортировка по оценке
    sortSubjectsByGrade() {
        const container = document.querySelector('.card-body');
        const cards = Array.from(container.querySelectorAll('.grade-card'));
        
        cards.sort((a, b) => {
            const gradeA = parseInt(a.dataset.grade);
            const gradeB = parseInt(b.dataset.grade);
            return gradeB - gradeA; // По убыванию
        });
        
        cards.forEach(card => container.appendChild(card));
    }

    // Сортировка по названию
    sortSubjectsByName() {
        const container = document.querySelector('.card-body');
        const cards = Array.from(container.querySelectorAll('.grade-card'));
        
        cards.sort((a, b) => {
            const nameA = a.dataset.subject;
            const nameB = b.dataset.subject;
            return nameA.localeCompare(nameB);
        });
        
        cards.forEach(card => container.appendChild(card));
    }

    // Экспорт данных
    exportGrades() {
        const exportData = {
            student: this.gradesData.student,
            currentSemester: this.currentSemester,
            semesterData: this.gradesData.semesters[this.currentSemester],
            exportDate: new Date().toISOString()
        };

        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `успеваемость_${exportData.student.name}_семестр${exportData.currentSemester}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        StudApp.showNotification('Данные успешно экспортированы', 'success');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    window.gradesManager = new GradesManager();
});