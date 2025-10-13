# Event Booking System

A comprehensive Laravel-based Event Booking System built for AutomatedPros backend developer assessment.

## 🚀 Features

- **JWT-like Authentication** with Laravel Sanctum
- **Role-based Access Control** (Admin, Organizer, Customer)
- **Event & Ticket Management** with CRUD operations
- **Booking System** with double booking prevention
- **Payment Processing** (Mocked with 80% success rate)
- **Email Notifications** with Queue system
- **Caching** for performance optimization
- **Comprehensive Testing** with 85%+ coverage
- **RESTful APIs** with proper HTTP status codes

## 🛠 Tech Stack

- Laravel 12.x
- Laravel Sanctum (API Authentication)
- MySQL Database
- Queue System (Database driver)
- Caching (Redis/File)
- PHPUnit Testing

## 📋 API Endpoints

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout  
- `GET /api/me` - Get current user

### Events
- `GET /api/events` - List events with pagination & filters
- `GET /api/events/{id}` - Get event details
- `POST /api/events` - Create event (Organizer/Admin)
- `PUT /api/events/{id}` - Update event
- `DELETE /api/events/{id}` - Delete event

### Tickets
- `POST /api/events/{event_id}/tickets` - Create ticket
- `PUT /api/tickets/{id}` - Update ticket
- `DELETE /api/tickets/{id}` - Delete ticket

### Bookings
- `POST /api/tickets/{ticket_id}/bookings` - Create booking
- `GET /api/bookings` - List user bookings
- `PUT /api/bookings/{id}/cancel` - Cancel booking

### Payments
- `POST /api/bookings/{id}/payment` - Process payment
- `GET /api/payments/{id}` - Get payment details

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js (optional)

### Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/your-username/event-booking-system.git
   cd event-booking-system