# Marketplace

A simple PHP & MySQL marketplace web application for clients, sellers, and admins.

## Features

- User authentication (login/logout)
- Product browsing and filtering
- Wishlist management
- Shopping cart with quantity updates
- Order placement with delivery address
- Admin panel for managing users, products, and orders
- Seller panel for managing their own products

## Technologies

- PHP
- MySQL/MariaDB
- HTML/CSS (Bootstrap)
- JavaScript (optional)

## Setup

1. **Clone the repository:**
   ```sh
   git clone https://github.com/Yomnahaouel/Marketplace
   ```

2. **Import the database:**
   - Use `marketdb (2).sql` in phpMyAdmin or MySQL CLI.

3. **Configure database connection:**
   - Edit `db.php` with your database credentials.

4. **Run locally:**
   - Place the project in your XAMPP/WAMP `htdocs` folder.
   - Start Apache and MySQL.
   - Visit [http://localhost/Marketplace/compte/login.php](http://localhost/Marketplace/compte/login.php)

## Usage

- Register or log in as a client, seller, or admin.
- Browse products, add to wishlist or cart.
- Place orders with a delivery address.
- Admins can manage products, users, and orders.
- Sellers can manage their own products.

## License

MIT

