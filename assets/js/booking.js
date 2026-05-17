// Booking JavaScript for Ayu Hotel

document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking-form');
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');
    const nightsDisplay = document.getElementById('nights');
    const totalPriceDisplay = document.getElementById('total-price');
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    if (checkInInput) {
        checkInInput.setAttribute('min', today);
    }
    
    // Calculate nights and update price
    function updateBookingSummary() {
        if (checkInInput && checkOutInput && nightsDisplay) {
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);
            
            if (checkIn && checkOut && checkOut > checkIn) {
                const nights = calculateNights(checkIn, checkOut);
                nightsDisplay.textContent = nights;
                
                // Calculate total price based on selected room
                const selectedRoom = document.querySelector('.room-card.selected');
                if (selectedRoom) {
                    const pricePerNight = parseFloat(selectedRoom.dataset.price);
                    const total = nights * pricePerNight;
                    if (totalPriceDisplay) {
                        totalPriceDisplay.textContent = '$' + total.toFixed(2);
                    }
                }
            }
        }
    }
    
    // Event listeners for date changes
    if (checkInInput) {
        checkInInput.addEventListener('change', function() {
            if (checkOutInput) {
                checkOutInput.setAttribute('min', this.value);
            }
            updateBookingSummary();
        });
    }
    
    if (checkOutInput) {
        checkOutInput.addEventListener('change', updateBookingSummary);
    }
    
    // Room selection
    const roomCards = document.querySelectorAll('.room-card');
    roomCards.forEach(card => {
        card.addEventListener('click', function() {
            roomCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            updateBookingSummary();
        });
    });
    
    // Form submission
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateBookingForm()) {
                return;
            }
            
            // Submit form data
            const formData = new FormData(bookingForm);
            // Add AJAX submission here
            console.log('Booking submitted:', Object.fromEntries(formData));
        });
    }
});

function validateBookingForm() {
    const checkIn = document.getElementById('check-in').value;
    const checkOut = document.getElementById('check-out').value;
    const guests = document.getElementById('guests').value;
    const selectedRoom = document.querySelector('.room-card.selected');
    
    if (!checkIn || !checkOut) {
        alert('Please select check-in and check-out dates');
        return false;
    }
    
    if (new Date(checkOut) <= new Date(checkIn)) {
        alert('Check-out date must be after check-in date');
        return false;
    }
    
    if (!guests || guests < 1) {
        alert('Please select number of guests');
        return false;
    }
    
    if (!selectedRoom) {
        alert('Please select a room');
        return false;
    }
    
    return true;
}
