# API Documentation

## Overview

This document describes the API endpoints available for the Ayu Hotel website.

## Base URL

```
https://www.ayuhotel.com/api/
```

## Authentication

Most endpoints require authentication. Include your API key in the header:

```
Authorization: Bearer YOUR_API_KEY
```

## Endpoints

### Booking API

#### Create Booking
```http
POST /api/booking
Content-Type: application/json

{
  "check_in": "2024-01-15",
  "check_out": "2024-01-17",
  "guests": 2,
  "room_type": "deluxe",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "+251911234567",
  "special_requests": "Late check-in",
  "promo_code": "DISCOUNT10"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Booking successful",
  "booking_ref": "AYU123456789",
  "booking_id": 123
}
```

#### Get Booking Details
```http
GET /api/booking/{booking_ref}
Authorization: Bearer YOUR_API_KEY
```

**Response:**
```json
{
  "success": true,
  "booking": {
    "booking_ref": "AYU123456789",
    "check_in": "2024-01-15",
    "check_out": "2024-01-17",
    "nights": 2,
    "guests": 2,
    "room_type": "deluxe",
    "total_price": 240.00,
    "status": "confirmed"
  }
}
```

#### Cancel Booking
```http
DELETE /api/booking/{booking_ref}
Authorization: Bearer YOUR_API_KEY
```

**Response:**
```json
{
  "success": true,
  "message": "Booking cancelled successfully"
}
```

### Rooms API

#### Get Available Rooms
```http
GET /api/rooms?check_in=2024-01-15&check_out=2024-01-17&guests=2
```

**Response:**
```json
{
  "success": true,
  "rooms": [
    {
      "id": 1,
      "name": "Standard Room",
      "type": "standard",
      "price": 80,
      "capacity": 2,
      "available": true
    },
    {
      "id": 2,
      "name": "Deluxe Room",
      "type": "deluxe",
      "price": 120,
      "capacity": 2,
      "available": true
    }
  ]
}
```

#### Get Room Details
```http
GET /api/rooms/{room_id}
```

**Response:**
```json
{
  "success": true,
  "room": {
    "id": 1,
    "name": "Standard Room",
    "type": "standard",
    "price": 80,
    "capacity": 2,
    "size": "25 sqm",
    "bed": "Queen Bed",
    "description": "Comfortable standard room",
    "amenities": ["WiFi", "Air Conditioning", "TV"],
    "images": ["standard-1.jpg", "standard-2.jpg"]
  }
}
```

### Events API

#### Get Events
```http
GET /api/events
```

**Response:**
```json
{
  "success": true,
  "events": [
    {
      "id": 1,
      "name": "Wedding Reception",
      "type": "wedding",
      "capacity": 200,
      "price": "Starting from $3000",
      "available": true
    }
  ]
}
```

#### Book Event
```http
POST /api/events/book
Content-Type: application/json

{
  "event_id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+251911234567",
  "date": "2024-06-15",
  "guests": 150,
  "special_requests": "Vegetarian options"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Event booking successful",
  "booking_ref": "EVT123456789"
}
```

### Contact API

#### Submit Contact Form
```http
POST /api/contact
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+251911234567",
  "subject": "Inquiry",
  "message": "I have a question about booking"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Message sent successfully"
}
```

### Payment API

#### Process Payment
```http
POST /api/payment
Content-Type: application/json

{
  "booking_id": 123,
  "card_number": "4111111111111111",
  "expiry": "12/25",
  "cvv": "123",
  "card_name": "John Doe"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment successful",
  "transaction_id": "TXN123456789"
}
```

### Offers API

#### Get Active Offers
```http
GET /api/offers
```

**Response:**
```json
{
  "success": true,
  "offers": [
    {
      "id": 1,
      "title": "Early Bird Special",
      "description": "Book 30 days in advance and get 20% off",
      "discount": 20,
      "code": "EARLY20",
      "valid_until": "2024-12-31"
    }
  ]
}
```

#### Validate Promo Code
```http
POST /api/offers/validate
Content-Type: application/json

{
  "code": "EARLY20",
  "total_amount": 240.00
}
```

**Response:**
```json
{
  "success": true,
  "valid": true,
  "discount": 48.00,
  "final_amount": 192.00
}
```

## Error Responses

All endpoints may return error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": "Error message for this field"
  }
}
```

### HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request parameters
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Access denied
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server error

## Rate Limiting

API requests are limited to 100 requests per minute per IP address.

## Support

For API support, contact: api-support@ayuhotel.com
