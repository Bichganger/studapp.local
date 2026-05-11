/**
 * ЖИВАЯ НЕЙРОСЕТЬ ЗНАНИЙ
 * Уникальная визуальная фишка проекта Учеба24
 */

class NeuralNetwork {
    constructor() {
        this.canvas = document.getElementById('neuralNetworkCanvas');
        if (!this.canvas) return;
        
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.connections = [];
        this.mouseX = 0;
        this.mouseY = 0;
        this.role = document.body.classList.contains('role-student') ? 'student' : 
                   document.body.classList.contains('role-teacher') ? 'teacher' : 'admin';
        
        this.init();
        this.animate();
        this.bindEvents();
    }
    
    init() {
        this.resize();
        this.createParticles();
    }
    
    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }
    
    createParticles() {
        const particleCount = Math.min(80, Math.floor((this.canvas.width * this.canvas.height) / 15000));
        this.particles = [];
        
        for (let i = 0; i < particleCount; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                size: Math.random() * 3 + 2,
                connections: []
            });
        }
    }
    
    bindEvents() {
        window.addEventListener('resize', () => this.resize());
        
        // Удалена анимация курсора для производительности
    }
    
    createBurst(x, y) {
        for (let i = 0; i < 5; i++) {
            const particle = {
                x: x,
                y: y,
                vx: (Math.random() - 0.5) * 5,
                vy: (Math.random() - 0.5) * 5,
                size: Math.random() * 4 + 2,
                life: 1
            };
            this.particles.push(particle);
        }
        
        setTimeout(() => {
            this.particles = this.particles.filter(p => !p.life || p.life > 0);
        }, 1000);
    }
    
    updateParticles() {
        this.particles.forEach((particle, index) => {
            if (particle.life !== undefined) {
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.life -= 0.02;
                return;
            }
            
            // Движение
            particle.x += particle.vx;
            particle.y += particle.vy;
            
            // Отскок от стен
            if (particle.x < 0 || particle.x > this.canvas.width) particle.vx *= -1;
            if (particle.y < 0 || particle.y > this.canvas.height) particle.vy *= -1;
            
            // Удалена анимация от мыши для производительности
            
            // Ограничение скорости
            const speed = Math.sqrt(particle.vx ** 2 + particle.vy ** 2);
            if (speed > 2) {
                particle.vx = (particle.vx / speed) * 2;
                particle.vy = (particle.vy / speed) * 2;
            }
        });
        
        // Удаление частиц с жизнью
        this.particles = this.particles.filter(p => !p.life || p.life > 0);
    }
    
    drawParticles() {
        const gradient = this.getGradient();
        
        this.particles.forEach(particle => {
            if (particle.life !== undefined) {
                this.ctx.globalAlpha = particle.life;
            }
            
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fillStyle = gradient;
            this.ctx.fill();
            
            if (particle.life !== undefined) {
                this.ctx.globalAlpha = 1;
            }
        });
    }
    
    drawConnections() {
        const maxDistance = 150;
        const gradient = this.getGradient();
        
        this.particles.forEach((p1, i) => {
            this.particles.slice(i + 1).forEach(p2 => {
                const dx = p1.x - p2.x;
                const dy = p1.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < maxDistance) {
                    const opacity = (1 - distance / maxDistance) * 0.5;
                    this.ctx.beginPath();
                    this.ctx.moveTo(p1.x, p1.y);
                    this.ctx.lineTo(p2.x, p2.y);
                    this.ctx.strokeStyle = `rgba(255, 255, 255, ${opacity})`;
                    this.ctx.lineWidth = 1;
                    this.ctx.stroke();
                }
            });
        });
    }
    
    getGradient() {
        // Фиксированный градиент без отслеживания мыши
        if (this.role === 'student') {
            return 'rgba(102, 126, 234, 0.6)';
        } else if (this.role === 'teacher') {
            return 'rgba(240, 147, 251, 0.6)';
        } else {
            return 'rgba(79, 172, 254, 0.6)';
        }
    }
    
    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.updateParticles();
        this.drawConnections();
        this.drawParticles();
        
        requestAnimationFrame(() => this.animate());
    }
}

/**
 * Эффект конфетти при успехе
 */
function createSuccessParticles(x, y) {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'];
    
    for (let i = 0; i < 30; i++) {
        const particle = document.createElement('div');
        particle.className = 'success-particle';
        particle.style.left = x + 'px';
        particle.style.top = y + 'px';
        particle.style.width = (Math.random() * 10 + 5) + 'px';
        particle.style.height = particle.style.width;
        particle.style.background = colors[Math.floor(Math.random() * colors.length)];
        particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
        
        const angle = Math.random() * Math.PI * 2;
        const velocity = Math.random() * 200 + 100;
        const tx = Math.cos(angle) * velocity;
        const ty = Math.sin(angle) * velocity;
        
        particle.style.setProperty('--tx', tx + 'px');
        particle.style.setProperty('--ty', ty + 'px');
        
        document.body.appendChild(particle);
        
        setTimeout(() => particle.remove(), 1500);
    }
}

/**
 * Умное приветствие
 */
function getSmartGreeting(name) {
    const hour = new Date().getHours();
    
    let timeGreeting;
    if (hour >= 5 && hour < 12) {
        timeGreeting = 'Доброе утро';
    } else if (hour >= 12 && hour < 18) {
        timeGreeting = 'Добрый день';
    } else if (hour >= 18 && hour < 23) {
        timeGreeting = 'Добрый вечер';
    } else {
        timeGreeting = 'Доброй ночи';
    }
    
    const emojis = {
        student: '🎓',
        teacher: '👨‍🏫',
        admin: '🛡️'
    };
    
    const role = document.body.classList.contains('role-student') ? 'student' :
                document.body.classList.contains('role-teacher') ? 'teacher' : 'admin';
    
    const emoji = emojis[role] || '👤';
    const shortName = name.split(' ')[0];
    
    return `${timeGreeting}, <strong>${shortName}</strong> ${emoji}`;
}

/**
 * Инициализация при загрузке
 */
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация нейросети
    new NeuralNetwork();
    
    // Удален параллакс эффект для производительности
    
    // Анимация появления элементов
    const animatedElements = document.querySelectorAll('.neural-fade-in');
    animatedElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.animationDelay = `${index * 0.1}s`;
        
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.animation = `fadeInUp 0.6s ease-out ${index * 0.1}s forwards`;
        }, 50);
    });
});
    
