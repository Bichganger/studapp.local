// простой JS для панелей


function showMsg(text, type) {
    const div = document.createElement('div');
    div.className = 'alert alert-' + (type == 'error' ? 'danger' : 'success') + ' alert-dismissible fade show';
    div.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:250px;';
    div.innerHTML = text + '<button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>';
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

// загрузка оценок для студента
function loadGrades() {
    fetch('admin/api.php?action=get_my_grades')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#gradesTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.grades.map(g => `
                <tr>
                    <td>${g.subject || ''}</td>
                    <td>${g.grade || '-'}</td>
                    <td>${g.comment || '-'}</td>
                    <td>${g.created_at ? g.created_at.split(' ')[0] : ''}</td>
                </tr>
            `).join('');
        });
}

// загрузка расписания для студента
function loadSchedule() {
    fetch('admin/api.php?action=get_my_schedule')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#scheduleTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.schedule.map(s => `
                <tr>
                    <td>${s.day || ''}</td>
                    <td>${s.group_name || ''}</td>
                    <td>${s.subject || ''}</td>
                    <td>${s.time_start || ''}</td>
                    <td>${s.type || ''}</td>
                </tr>
            `).join('');
        });
}

// инициализация
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('gradesTable')) loadGrades();
    if (document.getElementById('scheduleTable')) loadSchedule();
});
