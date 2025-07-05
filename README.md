
Built by https://www.blackbox.ai

---

# Blog Management System

## Project Overview
The Blog Management System is a simple web application built using PHP and MySQL for managing blog posts and categories. Users can register, log in, and perform CRUD operations on blog posts and categories. The system provides a user-friendly interface for adding, editing, and deleting blog entries, as well as categorizing them appropriately.

## Installation
To set up the Blog Management System on your local machine, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/your-username/blog-management.git
   cd blog-management
   ```

2. **Configure the Database:**
   - Create a MySQL database named `blog_management`.
   - Run the necessary SQL scripts to create the required tables (`users`, `blogs`, `blog_categories`). Sample SQL scripts can be found in the project if provided.

3. **Update Database Configuration:**
   - Open `config.php` and update the database credentials as follows:
   ```php
   $host = "localhost";      // Your database host
   $user = "root";           // Your database username
   $password = "";           // Your database password
   $database = "blog_management"; // Your database name
   ```

4. **Run the PHP Server:**
   You can use the built-in PHP server to run the application:
   ```bash
   php -S localhost:8000
   ```
   Access the application in your browser at `http://localhost:8000`.

## Usage
1. **Log In / Register:**
   - Open `login.php` and enter your username and password to log in. Ensure you have the necessary users set up in the database.
   
2. **Manage Blogs:**
   - After logging in, you'll be able to view the blog list (`blog_list.php`), add new blogs (`blog_add.php`), and manage existing blogs.

3. **Manage Categories:**
   - Access the category list (`category_list.php`) to view, add, edit, or delete blog categories.

## Features
- User authentication (login/logout).
- CRUD operations for blog posts.
- CRUD operations for blog categories.
- Image upload functionality for blog posts.
- SEO-friendly URL slugs for blogs and categories.
- Responsive design with Bootstrap.

## Dependencies
This project does not include a `package.json` file, but it relies on the following technologies:
- PHP
- MySQL
- Bootstrap CSS library (linked directly in HTML files)

## Project Structure
```
.
├── config.php              // Database configuration
├── login.php               // User login functionality
├── logout.php              // User logout functionality
├── functions.php           // Helper functions
├── category_list.php       // List of blog categories
├── category_add.php        // Add new blog category
├── category_edit.php       // Edit existing blog category
├── category_delete.php      // Delete blog category
├── blog_list.php           // List of blogs
├── blog_add.php            // Add new blog post
├── blog_edit.php           // Edit existing blog post
├── blog_delete.php         // Delete blog post
```

## License
This project is open-source and available under the MIT License.