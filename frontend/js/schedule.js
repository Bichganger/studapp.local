// js/schedule.js - Функционал для страницы расписания

class ScheduleManager {
    constructor() {
        this.schedule = this.loadSchedule();
        this.currentWeek = 7; // Текущая учебная неделя
        this.init();
    }

    // Загрузка расписания из localStorage
    loadSchedule() {
        const saved = localStorage.getItem('studapp_schedule');
        if (saved) {
            return JSON.parse(saved);
        }
        // Расписание по умолчанию (можно расширить)
        return this.getDefaultSchedule();
    }

    // Расписание по умолчанию
    getDefaultSchedule() {
        return {
            currentWeek: {
                monday: [
                    {
                        id: 1,
                        subject: "Математический анализ",
                        type: "lecture",
                        time: "09:00",
                        duration: 1.5,
                        room: "Ауд. 201",
                        teacher: "Лесная М.А.",
                        description: ""
                    },
                    {
                        id: 2,
                        subject: "Программирование",
                        type: "practice",
                        time: "10:45",
                        duration: 1.5,
                        room: "Комп. класс 3",
                        teacher: "Петров И.С.",
                        description: "Лабораторная работа"
                    }
                ],
                tuesday: [
                    // ... другие дни
                ]
            },
            nextWeek: {
                // Расписание на следующую неделю
            }
        };
    }

    init() {
        this.setupEventListeners();
        this.updateCurrentDay();
        this.updateTimeToLessons();
    }

    setupEventListeners() {
        // Переключение между неделями
        const weekButtons = document.querySelectorAll('[data-week]');
        weekButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const week = btn.dataset.week;
                this.switchWeek(week);
            });
        });

        // Фильтрация
        const filterItems = document.querySelectorAll('[data-filter]');
        filterItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                this.applyFilter(item.dataset.filter);
            });
        });

        // Добавление пары
        const addLessonForm = document.getElementById('addLessonForm');
        if (addLessonForm) {
            addLessonForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleAddLesson();
            });
        }

        // Экспорт расписания
        const exportBtn = document.getElementById('exportScheduleBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportSchedule();
            });
        }
    }

    // Переключение недели
    switchWeek(week) {
        const weekText = week === 'current' ? 'текущей' : 'следующей';
        StudApp.showNotification(`Показана ${weekText} неделя`, 'info');
        
        // Обновляем активные кнопки
        document.querySelectorAll('[data-week]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.week === week);
        });
    }

    // Применение фильтра
    applyFilter(filter) {
        const lessonItems = document.querySelectorAll('.lesson-item');
        
        lessonItems.forEach(item => {
            let shouldShow = true;
            
            switch(filter) {
                case 'lecture':
                    shouldShow = item.querySelector('.lesson-type').textContent === 'Лекция';
                    break;
                case 'practice':
                    shouldShow = item.querySelector('.lesson-type').textContent === 'Практика';
                    break;
                case 'today':
                    shouldShow = item.closest('.day-card').querySelector('.today');
                    break;
                default:
                    shouldShow = true;
            }
            
            item.style.display = shouldShow ? 'flex' : 'none';
        });
    }

    // Обновление текущего дня
    updateCurrentDay() {
        const today = new Date().getDay();
        const dayMap = { 1: 'monday', 2: 'tuesday', 3: 'wednesday', 4: 'thursday', 5: 'friday', 6: 'saturday' };
        const todayId = dayMap[today];
        
        if (todayId) {
            document.querySelectorAll('.day-card').forEach(card => {
                card.classList.toggle('active', card.dataset.day === todayId);
            });
        }
    }

    // Обновление времени до ближайших пар
    updateTimeToLessons() {
        const now = new Date();
        const currentTime = now.getHours() * 60 + now.getMinutes();
        
        document.querySelectorAll('.lesson-item').forEach(item => {
            const timeText = item.querySelector('.lesson-time').textContent.split(' - ')[0];
            const [hours, minutes] = timeText.split(':').map(Number);
            const lessonTime = hours * 60 + minutes;
            
            const timeDiff = lessonTime - currentTime;
            const timeElement = item.querySelector('.time-diff');
            
            if (timeElement) {
                if (timeDiff > 0 && timeDiff <= 60) {
                    timeElement.textContent = `Через ${timeDiff} мин`;
                    timeElement.className = 'text-success';
                } else if (timeDiff > 60) {
                    const hoursLeft = Math.floor(timeDiff / 60);
                    const minutesLeft = timeDiff % 60;
                    timeElement.textContent = `Через ${hoursLeft}ч ${minutesLeft}мин`;
                    timeElement.className = 'text-muted';
                } else {
                    timeElement.textContent = 'Прошло';
                    timeElement.className = 'text-secondary';
                }
            }
        });
    }

    // Добавление новой пары
    handleAddLesson() {
        const form = document.getElementById('addLessonForm');
        const formData = new FormData(form);
        
        const newLesson = {
            id: Date.now(),
            subject: formData.get('subject'),
            type: formData.get('type'),
            day: formData.get('day'),
            time: formData.get('time'),
            duration: parseFloat(formData.get('duration') || '1.5'),
            room: formData.get('room'),
            teacher: formData.get('teacher') || '',
            description: '',
            createdAt: new Date().toISOString()
        };

        // Здесь можно добавить логику сохранения в расписание (localStorage)
        // Для демонстрации просто покажем уведомление
        StudApp.showNotification(`Пара "${newLesson.subject}" добавлена`, 'success');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('addLessonModal'));
        modal.hide();
        form.reset();
    }

    // Экспорт расписания
    exportSchedule() {
        const scheduleData = {
            title: "Расписание группы ИТ-321",
            week: this.currentWeek,
            schedule: this.schedule,
            exportedAt: new Date().toISOString()
        };

        const blob = new Blob([JSON.stringify(scheduleData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `schedule_week${this.currentWeek}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        StudApp.showNotification('Расписание экспортировано в JSON', 'success');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    window.scheduleManager = new ScheduleManager();
    
    // Обновление времени каждую минуту
    setInterval(() => {
        if (window.scheduleManager) {
            window.scheduleManager.updateTimeToLessons();
        }
    }, 60000);
});