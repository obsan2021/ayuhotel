// Payment Processing JavaScript for Ayu Hotel

document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('payment-form');
    const cardNumberInput = document.getElementById('card-number');
    const expiryInput = document.getElementById('expiry');
    const cvvInput = document.getElementById('cvv');
    
    // Format card number
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue.substring(0, 19);
        });
    }
    
    // Format expiry date
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value.substring(0, 5);
        });
    }
    
    // CVV validation
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    }
    
    // Payment form submission
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validatePaymentForm()) {
                return;
            }
            
            // Show loading state
            const submitBtn = paymentForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            
            // Simulate payment processing
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Pay Now';
                alert('Payment processed successfully!');
                // Redirect to confirmation page
                window.location.href = 'confirmation.html';
            }, 2000);
        });
    }
});

function validatePaymentForm() {
    const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
    const expiry = document.getElementById('expiry').value;
    const cvv = document.getElementById('cvv').value;
    const cardName = document.getElementById('card-name').value;
    
    // Validate card number (basic Luhn algorithm)
    if (!luhnCheck(cardNumber)) {
        alert('Invalid card number');
        return false;
    }
    
    // Validate expiry date
    if (!validateExpiry(expiry)) {
        alert('Invalid expiry date');
        return false;
    }
    
    // Validate CVV
    if (cvv.length < 3 || cvv.length > 4) {
        alert('Invalid CVV');
        return false;
    }
    
    // Validate cardholder name
    if (cardName.trim().length < 2) {
        alert('Please enter cardholder name');
        return false;
    }
    
    return true;
}

function luhnCheck(cardNumber) {
    if (!/^\d+$/.test(cardNumber)) return false;
    if (cardNumber.length < 13 || cardNumber.length > 19) return false;
    
    let sum = 0;
    let isEven = false;
    
    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cardNumber[i], 10);
        
        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        
        sum += digit;
        isEven = !isEven;
    }
    
    return sum % 10 === 0;
}

function validateExpiry(expiry) {
    const parts = expiry.split('/');
    if (parts.length !== 2) return false;
    
    const month = parseInt(parts[0], 10);
    const year = parseInt('20' + parts[1], 10);
    
    if (month < 1 || month > 12) return false;
    
    const now = new Date();
    const expiryDate = new Date(year, month - 1);
    
    return expiryDate > now;
}
