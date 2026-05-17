# Ayu Hotel Website

A luxury hotel website for Ayu Hotel located in Adama, Ethiopia.

## Features

- Responsive design for all devices
- Multi-language support (English, Amharic, Oromo)
- Online booking system
- Room management
- Event booking
- Gallery management
- Contact form
- Admin panel for content management

## Directory Structure

```
ayuhotel/
├── index.html              # Homepage
├── rooms.html             # Rooms & Suites page
├── dining.html            # Dining page
├── facilities.html        # Facilities page
├── events.html            # Events page
├── gallery.html           # Photo gallery
├── location.html          # Location page
├── offers.html            # Special offers
├── booking.html           # Booking page
├── about.html             # About us page
├── blog.html              # Blog page
├── contact.html           # Contact page
├── 404.html               # Error page
├── sitemap.xml            # Sitemap
├── robots.txt             # Robots file
├── .htaccess              # Apache configuration
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── images/           # Images
│   ├── fonts/            # Font files
│   ├── icons/            # Icon files
│   └── videos/           # Video files
├── data/                  # JSON data files
├── includes/              # Reusable HTML components
├── php/                   # Backend PHP files
├── admin/                 # Admin panel
├── languages/             # Language files
├── backups/               # Database backups
├── logs/                  # Error logs
└── docs/                  # Documentation
```

## Installation

1. Copy all files to your web server
2. Configure database connection in `php/config.php`
3. Import database schema (create tables)
4. Set up admin user in database
5. Configure email settings in `php/config.php`
6. Set file permissions for uploads directory

## Database Setup

Create the following tables:

- `admin_users` - Admin panel users
- `bookings` - Room bookings
- `rooms` - Room information
- `events` - Event information
- `gallery` - Gallery images
- `contact_messages` - Contact form submissions
- `payments` - Payment records
- `offers` - Special offers

## Configuration

Edit `php/config.php` to configure:

- Database credentials
- Email settings (SMTP)
- Payment gateway (Stripe/PayPal)
- Site settings
- Security settings

## Admin Panel

Access the admin panel at `/admin/`

Default login credentials (change after first login):
- Username: admin
- Password: (Set during installation)

## Features

### Frontend
- Responsive design
- Multi-language support
- Online booking system
- Image gallery with lightbox
- Contact form with email notifications
- Newsletter signup

### Backend
- Admin panel with authentication
- Booking management
- Room management
- Event management
- Gallery management
- Contact message management

## Security

- Password hashing using bcrypt
- SQL injection prevention with prepared statements
- XSS protection
- CSRF protection (implement as needed)
- Secure session management

## Support

For support and questions, contact:
- Email: info@ayuhotel.com
- Phone: +251 11 123 4567

## License

Copyright © 2024 Ayu Hotel. All rights reserved.
