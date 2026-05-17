// Form Validation JavaScript for Ayu Hotel

class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.validators = {};
        this.init();
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }
    
    addValidator(fieldName, validatorFn, errorMessage) {
        this.validators[fieldName] = {
            validator: validatorFn,
            message: errorMessage
        };
    }
    
    validateField(fieldName, value) {
        const validator = this.validators[fieldName];
        if (!validator) return { valid: true };
        
        const isValid = validator.validator(value);
        return {
            valid: isValid,
            message: isValid ? '' : validator.message
        };
    }
    
    validateForm() {
        const formData = new FormData(this.form);
        const errors = {};
        
        for (const [fieldName, validator] of Object.entries(this.validators)) {
            const value = formData.get(fieldName);
            const result = this.validateField(fieldName, value);
            
            if (!result.valid) {
                errors[fieldName] = result.message;
            }
        }
        
        return {
            valid: Object.keys(errors).length === 0,
            errors
        };
    }
    
    handleSubmit(e) {
        const result = this.validateForm();
        
        if (!result.valid) {
            e.preventDefault();
            this.displayErrors(result.errors);
        }
    }
    
    displayErrors(errors) {
        // Clear previous errors
        this.form.querySelectorAll('.error-message').forEach(el => el.remove());
        this.form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
        
        // Display new errors
        for (const [fieldName, message] of Object.entries(errors)) {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('has-error');
                
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.textContent = message;
                field.parentNode.appendChild(errorElement);
            }
        }
    }
}

// Common validators
const validators = {
    required: (value) => value && value.trim().length > 0,
    email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    phone: (value) => /^[\d\s\-\+\(\)]{10,}$/.test(value),
    minLength: (min) => (value) => value && value.length >= min,
    maxLength: (max) => (value) => value && value.length <= max,
    numeric: (value) => /^\d+$/.test(value),
    date: (value) => !isNaN(Date.parse(value)),
    age: (minAge) => (value) => {
        const birthDate = new Date(value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        return age >= minAge;
    }
};

// Initialize validators when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Contact form validation
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        const contactValidator = new FormValidator(contactForm);
        
        contactValidator.addValidator('name', validators.required, 'Name is required');
        contactValidator.addValidator('email', validators.email, 'Invalid email address');
        contactValidator.addValidator('phone', validators.phone, 'Invalid phone number');
        contactValidator.addValidator('message', validators.required, 'Message is required');
    }
    
    // Newsletter form validation
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        const newsletterValidator = new FormValidator(newsletterForm);
        
        newsletterValidator.addValidator('email', validators.email, 'Invalid email address');
    }
    
    // Real-time validation
    document.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim()) {
                this.classList.add('touched');
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('has-error')) {
                this.classList.remove('has-error');
                const errorElement = this.parentNode.querySelector('.error-message');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        });
    });
});
