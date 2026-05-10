(function() {
    'use strict';

    const KEYS = {
        NOTIFICATIONS: 'sync_notifications',
        SCHEDULE: 'sync_schedule',
        GRADES: 'sync_grades',
        ASSIGNMENTS: 'sync_assignments',
        JOURNAL: 'sync_journal',
        GROUPS: 'sync_groups',
        LIBRARY: 'sync_library',
        USERS: 'sync_users',
        SETTINGS: 'sync_settings'
    };

    function getNotifications() {
        const data = localStorage.getItem(KEYS.NOTIFICATIONS);
        return data ? JSON.parse(data) : [];
    }

    function addNotification(notification) {
        const notifications = getNotifications();
        notifications.unshift(notification);
        localStorage.setItem(KEYS.NOTIFICATIONS, JSON.stringify(notifications));
        window.dispatchEvent(new Event('storage'));
    }

    function sendNotification(title, message, target, group = null) {
        const notification = {
            id: Date.now(),
            title: title,
            message: message,
            target: target,
            group: group,
            date: new Date().toLocaleString('ru-RU'),
            read: false
        };
        addNotification(notification);
        return notification;
    }

    function markAsRead(id) {
        const notifications = getNotifications();
        const notif = notifications.find(n => n.id === id);
        if (notif) {
            notif.read = true;
            localStorage.setItem(KEYS.NOTIFICATIONS, JSON.stringify(notifications));
        }
    }

    function getNotificationsForUser(role, userGroup = null) {
        const notifications = getNotifications();
        return notifications.filter(n => {
            if (n.target === 'all') return true;
            if (n.target === 'students' && role === 'student') return true;
            if (n.target === 'teachers' && role === 'teacher') return true;
            if (n.target === 'group' && n.group === userGroup) return true;
            return false;
        });
    }

    function getSchedule() {
        const data = localStorage.getItem(KEYS.SCHEDULE);
        return data ? JSON.parse(data) : [];
    }

    function addSchedule(item) {
        const schedule = getSchedule();
        schedule.push(item);
        localStorage.setItem(KEYS.SCHEDULE, JSON.stringify(schedule));
        window.dispatchEvent(new Event('storage'));
    }

    function deleteSchedule(index) {
        const schedule = getSchedule();
        schedule.splice(index, 1);
        localStorage.setItem(KEYS.SCHEDULE, JSON.stringify(schedule));
        window.dispatchEvent(new Event('storage'));
    }

    function getGrades() {
        const data = localStorage.getItem(KEYS.GRADES);
        return data ? JSON.parse(data) : [];
    }

    function addGrade(grade) {
        const grades = getGrades();
        grades.push(grade);
        localStorage.setItem(KEYS.GRADES, JSON.stringify(grades));
        window.dispatchEvent(new Event('storage'));
    }

    function getAssignments() {
        const data = localStorage.getItem(KEYS.ASSIGNMENTS);
        return data ? JSON.parse(data) : [];
    }

    function addAssignment(assignment) {
        const assignments = getAssignments();
        assignments.push(assignment);
        localStorage.setItem(KEYS.ASSIGNMENTS, JSON.stringify(assignments));
        window.dispatchEvent(new Event('storage'));
    }

    function updateAssignment(index, data) {
        const assignments = getAssignments();
        assignments[index] = { ...assignments[index], ...data };
        localStorage.setItem(KEYS.ASSIGNMENTS, JSON.stringify(assignments));
        window.dispatchEvent(new Event('storage'));
    }

    function getJournal() {
        const data = localStorage.getItem(KEYS.JOURNAL);
        return data ? JSON.parse(data) : [];
    }

    function addJournal(entry) {
        const journal = getJournal();
        journal.push(entry);
        localStorage.setItem(KEYS.JOURNAL, JSON.stringify(journal));
        window.dispatchEvent(new Event('storage'));
    }

    function getGroups() {
        const data = localStorage.getItem(KEYS.GROUPS);
        return data ? JSON.parse(data) : [];
    }

    function addGroup(group) {
        const groups = getGroups();
        groups.push(group);
        localStorage.setItem(KEYS.GROUPS, JSON.stringify(groups));
        window.dispatchEvent(new Event('storage'));
    }

    function deleteGroup(index) {
        const groups = getGroups();
        groups.splice(index, 1);
        localStorage.setItem(KEYS.GROUPS, JSON.stringify(groups));
        window.dispatchEvent(new Event('storage'));
    }

    function getLibrary() {
        const data = localStorage.getItem(KEYS.LIBRARY);
        return data ? JSON.parse(data) : [];
    }

    function addLibrary(item) {
        const library = getLibrary();
        library.push(item);
        localStorage.setItem(KEYS.LIBRARY, JSON.stringify(library));
        window.dispatchEvent(new Event('storage'));
    }

    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show fixed-top`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);
    }

    window.Sync = {
        getNotifications, addNotification, sendNotification, markAsRead, getNotificationsForUser,
        getSchedule, addSchedule, deleteSchedule,
        getGrades, addGrade,
        getAssignments, addAssignment, updateAssignment,
        getJournal, addJournal,
        getGroups, addGroup, deleteGroup,
        getLibrary, addLibrary,
        showNotification
    };

    window.addEventListener('storage', function() {
        window.dispatchEvent(new Event('sync-update'));
    });

})();