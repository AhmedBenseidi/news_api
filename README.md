# News API 📰

A robust and scalable Backend API for managing news content and dynamic advertisements (Banners), built with **Laravel**. This project is designed to be integrated seamlessly with mobile applications (like Flutter) or web frontends.

---

## 🚀 Key Features

- **News Management:** Full CRUD operations for news articles with category support.
- **Dynamic Banners:** A dedicated system to manage promotional banners for mobile apps.
- **Categorization:** Organize news content into various logical sections.
- **ImgBB Integration:** Efficient image hosting using **ImgBB API** for fast and reliable media delivery.
- **Secure Authentication:** Protected routes using **Laravel Sanctum** (Token-based).
- **Advanced Search:** Optimized endpoints for searching news by keywords.

---

## 🛠 Tech Stack

- **Framework:** Laravel 11 / 12
- **Database:** MySQL / PostgreSQL (Production ready for Railway)
- **Image Hosting:** ImgBB API
- **Authentication:** Laravel Sanctum
- **Runtime:** PHP 8.4
- **Development Environment:** Xubuntu Linux

---

## 📦 Installation & Setup

1.  **Clone the repository:**

    ```bash
    git clone [https://github.com/Ahmed-Benseidi/news-api.git](https://github.com/Ahmed-Benseidi/news-api.git)
    cd news-api
    ```

2.  **Install dependencies:**

    ```bash
    composer install
    ```

3.  **Environment Configuration:**
    Copy the example environment file and set your database and ImgBB API key.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    _Add your ImgBB key in `.env`:_ `IMGBB_API_KEY=your_key_here`

4.  **Database Migration:**

    ```bash
    php artisan migrate
    ```

5.  **Run the local server:**
    ```bash
    php artisan serve
    ```

---

## 🔗 API Endpoints (Quick Reference)

| Function      | Endpoint            | Method                   | Auth Required |
| :------------ | :------------------ | :----------------------- | :------------ |
| User Login    | `/api/login`        | `POST`                   | No            |
| Get Banners   | `/api/banners`      | `GET`                    | No            |
| Get News      | `/api/news`         | `GET`                    | No            |
| Create News   | `/api/news`         | `POST`                   | Yes           |
| Update Banner | `/api/banners/{id}` | `POST` (+ `_method=PUT`) | Yes           |

---

## 📱 Flutter Integration Guide

### 1. Simple Data Fetching

Use the `http` package to fetch news from your hosted URL.

```dart
Future<void> getNews() async {
  final url = "[https://your-app.railway.app/api/news](https://your-app.railway.app/api/news)";
  final response = await http.get(Uri.parse(url), headers: {
    'Accept': 'application/json',
  });

  if (response.statusCode == 200) {
    // Parse your JSON data here
  }
}
```
