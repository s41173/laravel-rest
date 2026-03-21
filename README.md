# Laravel REST API Demo

A lightweight **Laravel REST API** project built for demonstration purposes.

---

## 🚀 Features

- RESTful API using Laravel
- **MySQL** for persistent data
- **Redis (Railway Cloud)** for caching
- **JWT** for Token
- Whatsapp Notification for OTP Request
- Environment-based configuration via `.env`

---

## 🏗 Architecture / Tech Stack

Client (Browser / Postman)
│
▼
REST API
│
▼
┌───────────────┐
│ MySQL DB │ ←  | 
└───────────────┘
│
▼
┌───────────────┐
│ Redis Cache │ ← Railway Redis Cloud
└───────────────┘


## 📦 Collections / Endpoints

### General
| Method | Endpoint      | Description               |
|--------|--------------------------|----------------|
| GET    | api/                     | Ping           |       
| GET    | /city                    | City List      |
| GET    | /district/{id_kabupaten} | District List  |

### Auth
| Method | Endpoint      | Description         |
|--------|---------------|---------------------|
| POST   | /login        | Login               |       
| GET    | /decode       | Decode Token        |
| GET    | /logout       | Logout              |
| POST   | /forgot       | Forgot Password     |
| POST   | /otp          | Request OTP         |
| POST   | /verify       | Verify User         |

### Users (example)
| Method | Endpoint         | Description          |
|--------|------------------|----------------------|
| POST   | /register        | Register user        |
| PUT    | /update          | Update user          |
| PUT    | /change_password | Change user password |
| POST   | /updateImage     | Upload user image    |
| GET    | /get             | User information     |

---

## ⚙ Usage

## Postman Collection
Collection available on this repository.
Url : https://apiv1.lapakbenz.com/api/
Authentication : Bearer Token
------------------------------
Username : 082277014410
Password : s3retl4rav3l
