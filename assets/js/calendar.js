// Calendar JavaScript for Ayu Hotel

class HotelCalendar {
    constructor(elementId, options = {}) {
        this.element = document.getElementById(elementId);
        this.options = {
            minDate: options.minDate || new Date(),
            maxDate: options.maxDate || null,
            disabledDates: options.disabledDates || [],
            onSelect: options.onSelect || null,
            ...options
        };
        
        this.currentDate = new Date(this.options.minDate);
        this.selectedDate = null;
        
        this.init();
    }
    
    init() {
        this.render();
        this.attachEventListeners();
    }
    
    render() {
        if (!this.element) return;
        
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startingDay = firstDay.getDay();
        
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        let html = `
            <div class="calendar-header">
                <button class="calendar-nav prev-month">&lt;</button>
                <h3>${monthNames[month]} ${year}</h3>
                <button class="calendar-nav next-month">&gt;</button>
            </div>
            <div class="calendar-grid">
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>
        `;
        
        // Empty cells for days before the first day of the month
        for (let i = 0; i < startingDay; i++) {
            html += '<div class="calendar-day empty"></div>';
        }
        
        // Days of the month
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(year, month, day);
            const isDisabled = this.isDateDisabled(date);
            const isSelected = this.selectedDate && 
                date.toDateString() === this.selectedDate.toDateString();
            
            html += `
                <div class="calendar-day ${isDisabled ? 'disabled' : ''} ${isSelected ? 'selected' : ''}" 
                     data-date="${date.toISOString()}">
                    ${day}
                </div>
            `;
        }
        
        html += '</div>';
        this.element.innerHTML = html;
    }
    
    isDateDisabled(date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (date < today) return true;
        if (this.options.maxDate && date > this.options.maxDate) return true;
        
        return this.options.disabledDates.some(disabledDate => 
            date.toDateString() === disabledDate.toDateString()
        );
    }
    
    attachEventListeners() {
        const prevBtn = this.element.querySelector('.prev-month');
        const nextBtn = this.element.querySelector('.next-month');
        const days = this.element.querySelectorAll('.calendar-day:not(.empty):not(.disabled)');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                this.render();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                this.render();
            });
        }
        
        days.forEach(day => {
            day.addEventListener('click', () => {
                this.selectedDate = new Date(day.dataset.date);
                this.render();
                
                if (this.options.onSelect) {
                    this.options.onSelect(this.selectedDate);
                }
            });
        });
    }
}

// Initialize calendar when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const calendarElement = document.getElementById('booking-calendar');
    if (calendarElement) {
        new HotelCalendar('booking-calendar', {
            minDate: new Date(),
            onSelect: function(date) {
                console.log('Selected date:', date);
            }
        });
    }
});
