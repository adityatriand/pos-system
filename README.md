# POS System

A modern Point of Sale (POS) system built with Laravel backend and Vue.js frontend, designed for restaurant and retail businesses.

## 📈 Recent Updates

### Latest Release (August 2025)
- ✅ **Complete System Implementation** - Full POS system with all core modules
- ✅ **Docker Deployment** - Containerized application with Docker Compose
- ✅ **Frontend Framework** - Vue.js 3.5 with TypeScript and modern tooling
- ✅ **Backend API** - Laravel 12 with comprehensive REST API
- ✅ **Database Structure** - Complete MySQL schema with relationships
- ✅ **PDF Export** - Transaction reports with Laravel DomPDF
- ✅ **Real-time UI** - Responsive design with Tailwind CSS and Headless UI
- ✅ **State Management** - Pinia for Vue.js state management
- ✅ **Development Tools** - Vite for fast builds and hot reload

### Development Timeline
1. **Project Initialization** - Backend Laravel setup and project structure
2. **Core Models & Controllers** - Database schema, migrations, and API controllers
3. **Full Module Development** - Complete frontend and backend integration
4. **Docker Containerization** - Production-ready deployment configuration
5. **Documentation** - Comprehensive README and setup instructions

## 🚀 Features

### Core Modules
- **Branch Management** - Manage multiple business locations
- **Employee Management** - Track staff information and assignments
- **Item Management** - Manage food & beverage inventory globally
- **Branch Inventory** - Manage branch-specific stock levels and availability
- **Transaction Processing** - Complete sales with automatic 5% service charge
- **Sales History** - View and filter transaction records
- **Dashboard Analytics** - Business insights and reporting with inventory alerts

### Key Features
- **Multi-branch Support** - Handle multiple business locations with independent inventory
- **Branch-Specific Inventory** - Each branch maintains separate stock levels for items
- **Automatic Stock Deduction** - Stock decreases automatically when transactions complete
- **Low Stock Alerts** - Real-time alerts for items below minimum levels
- **Inventory Management** - View and update stock levels per branch
- **Automatic Service Charge** - 5% service charge on all transactions
- **Smart Transaction Validation** - Only show items available at selected branch
- **PDF Export** - Export sales reports as PDF
- **Responsive Design** - Works on desktop and mobile devices
- **Date Range Filtering** - Filter reports by custom date ranges
- **Top Items Analytics** - Track best-selling products based on actual sales
- **Branch Revenue Comparison** - Compare performance across branches
- **Form Validation** - Comprehensive validation with real-time error messages
- **Toast Notifications** - User-friendly success/error notifications
- **Loading States** - Visual feedback during data operations

## 🛠️ Technology Stack

### Backend
- **Laravel 12** - PHP framework
- **MySQL 8.0** - Database
- **Laravel DomPDF** - PDF generation
- **Docker** - Containerization

### Frontend
- **Vue.js 3.5** - JavaScript framework with Composition API
- **TypeScript 5.9** - Type safety and better development experience
- **Vite 7.1** - Fast build tool and development server
- **Tailwind CSS 3.4** - Modern utility-first styling
- **Pinia 3.0** - State management for Vue.js
- **Axios 1.11** - HTTP client for API communication
- **Vue Router 4.5** - Client-side navigation
- **Headless UI & Heroicons** - Accessible UI components and icons
- **Custom Composables** - Reusable validation and notification logic

## 📋 Prerequisites

- Docker and Docker Compose
- Git

## 🚀 Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd pos-system
   ```

2. **Start the application**
   ```bash
   chmod +x start.sh
   ./start.sh
   ```

3. **Access the application**
   - **Frontend**: http://localhost:3000
   - **Backend API**: http://localhost:8080/api
   - **Database**: localhost:3306

## 🔧 Manual Setup (Alternative)

If you prefer to set up manually or the automatic script doesn't work:

### 1. Start Docker Services
```bash
docker-compose up -d
```

### 2. Install Backend Dependencies
```bash
docker exec pos-laravel-app composer install
```

### 3. Set up Laravel
```bash
# Generate application key
docker exec pos-laravel-app php artisan key:generate --force

# Run migrations
docker exec pos-laravel-app php artisan migrate --force

# Seed database with sample data
docker exec pos-laravel-app php artisan db:seed

# Clear caches
docker exec pos-laravel-app php artisan config:clear
docker exec pos-laravel-app php artisan cache:clear
```

## 📊 Database

### Default Credentials
- **Database Name**: `pos_system`
- **Username**: `pos_user`
- **Password**: `password`
- **Root Password**: `root`

### Sample Data
The system comes with pre-populated sample data:
- 3 branches with different stock levels (Main Branch, Branch 2, Test Branch)
- 3 employees across branches with different roles
- 10 items (5 food, 5 beverages) with branch-specific inventory
- Sample transactions demonstrating the system functionality

## 🎯 Usage Guide

### 1. Managing Branches
- Navigate to "Branches" to add new locations
- Each branch can have multiple employees
- Branches can be activated/deactivated

### 2. Managing Employees
- Add employees and assign them to branches
- Set positions, salaries, and contact information
- Track hire dates and employment status

### 3. Managing Items
- Add food and beverage items globally
- Set prices, units, and categories
- Items are automatically assigned to all branches
- Use Branch Inventory to manage stock per location

### 4. Managing Branch Inventory
- Navigate to "Branch Inventory" to manage stock levels
- Select a branch to view its inventory
- Update stock quantities and minimum stock levels
- Monitor low stock alerts and availability status
- Each branch maintains independent inventory levels

### 5. Processing Transactions
- Select branch first (this determines available items)
- Choose employee from the selected branch
- Only items with stock at the selected branch are shown
- Add items to the transaction (stock is validated)
- System automatically calculates 5% service charge
- Choose payment method (cash, card, digital wallet)
- Stock automatically decreases when transaction completes

### 6. Viewing Sales History
- Filter by branch and date range
- Export filtered results to PDF
- View detailed transaction information with branch context

### 7. Dashboard Analytics
- View revenue summaries with date filtering
- Monitor low stock alerts across all branches
- See branch inventory summaries with stock levels
- Track top-selling items based on actual sales data
- Compare branch performance and revenue
- Real-time inventory status monitoring

## 🔌 API Endpoints

### Branches
- `GET /api/branches` - List all branches
- `POST /api/branches` - Create new branch
- `GET /api/branches/{id}` - Get branch details
- `PUT /api/branches/{id}` - Update branch
- `DELETE /api/branches/{id}` - Delete branch
- `GET /api/branches/{id}/employees` - Get employees for specific branch
- `GET /api/branches/{id}/items` - Get items available at specific branch

### Employees
- `GET /api/employees` - List all employees
- `POST /api/employees` - Create new employee
- `GET /api/employees/{id}` - Get employee details
- `PUT /api/employees/{id}` - Update employee
- `DELETE /api/employees/{id}` - Delete employee

### Items
- `GET /api/items` - List all items
- `POST /api/items` - Create new item
- `GET /api/items/{id}` - Get item details
- `PUT /api/items/{id}` - Update item
- `DELETE /api/items/{id}` - Delete item
- `GET /api/items/category/{category}` - Get items by category

### Transactions
- `GET /api/transactions` - List all transactions
- `POST /api/transactions` - Create new transaction
- `GET /api/transactions/{id}` - Get transaction details
- `GET /api/transactions/history/filter` - Filter transactions
- `GET /api/transactions/history/export-pdf` - Export to PDF

### Branch Inventory
- `GET /api/branch-items` - List all branch inventory records
- `POST /api/branch-items` - Create new branch inventory record
- `GET /api/branch-items/{id}` - Get specific branch inventory record
- `PUT /api/branch-items/{id}` - Update branch inventory record
- `DELETE /api/branch-items/{id}` - Remove item from branch
- `POST /api/branches/{id}/items` - Add multiple items to branch

### Dashboard
- `GET /api/dashboard/summary` - Get revenue summary
- `GET /api/dashboard/top-items` - Get top-selling items
- `GET /api/dashboard/branch-revenue` - Get branch revenue comparison
- `GET /api/dashboard/inventory-status` - Get inventory status and low stock alerts

## 📁 Project Structure

```
pos-system/
├── backend/                    # Laravel 12 backend
│   ├── app/
│   │   ├── Http/Controllers/Api/     # API controllers
│   │   │   ├── BranchController.php  # Branch management
│   │   │   ├── BranchItemController.php # Branch inventory
│   │   │   ├── EmployeeController.php # Employee management
│   │   │   ├── ItemController.php    # Item management
│   │   │   ├── TransactionController.php # Transaction processing
│   │   │   └── DashboardController.php # Analytics & reporting
│   │   └── Models/                   # Eloquent models
│   │       ├── Branch.php           # Branch model
│   │       ├── BranchItem.php       # Branch inventory model
│   │       ├── Employee.php         # Employee model
│   │       ├── Item.php             # Item model
│   │       ├── Transaction.php      # Transaction model
│   │       └── TransactionItem.php  # Transaction item model
│   ├── database/
│   │   ├── migrations/              # Database migrations
│   │   │   ├── 2025_08_12_184832_create_branches_table.php
│   │   │   ├── 2025_08_12_184833_create_employees_table.php
│   │   │   ├── 2025_08_12_184834_create_items_table.php
│   │   │   ├── 2025_08_12_184835_create_transactions_table.php
│   │   │   ├── 2025_08_12_184836_create_transaction_items_table.php
│   │   │   └── 2025_08_12_193255_create_branch_items_table.php
│   │   └── seeders/                 # Database seeders
│   │       ├── DatabaseSeeder.php   # Main seeder
│   │       └── BranchItemSeeder.php # Branch inventory seeder
│   ├── resources/views/transactions/ # PDF templates
│   │   └── pdf.blade.php            # Transaction PDF template
│   ├── routes/                      # Application routes
│   │   ├── api.php                  # API routes
│   │   ├── web.php                  # Web routes
│   │   └── console.php              # Console routes
│   └── composer.json                # PHP dependencies
├── frontend/                  # Vue.js 3.5 frontend
│   ├── src/
│   │   ├── components/            # Vue components
│   │   │   ├── FormError.vue      # Form validation errors
│   │   │   └── ToastNotification.vue # Toast notifications
│   │   ├── composables/           # Vue composables
│   │   │   ├── useValidation.ts   # Form validation logic
│   │   │   └── useToast.ts        # Toast notification logic
│   │   ├── views/                 # Page views
│   │   │   ├── Dashboard.vue      # Analytics dashboard
│   │   │   ├── Branches.vue       # Branch management
│   │   │   ├── BranchInventory.vue # Branch inventory management
│   │   │   ├── Employees.vue      # Employee management
│   │   │   ├── Items.vue          # Item management
│   │   │   ├── Transactions.vue   # Transaction processing
│   │   │   └── SalesHistory.vue   # Sales history & reports
│   │   ├── api/                   # API client
│   │   │   ├── client.ts          # Axios configuration
│   │   │   └── index.ts           # API endpoints
│   │   ├── router/                # Vue Router configuration
│   │   │   └── index.ts           # Router setup
│   │   ├── types/                 # TypeScript types
│   │   │   └── index.ts           # Type definitions
│   │   ├── App.vue                # Main app component
│   │   ├── main.ts                # Application entry point
│   │   └── style.css              # Global styles
│   ├── Dockerfile                 # Frontend Docker config
│   ├── package.json               # Node.js dependencies
│   ├── tailwind.config.mjs        # Tailwind CSS configuration
│   ├── vite.config.ts             # Vite build configuration
│   └── tsconfig.json              # TypeScript configuration
├── docker/                   # Docker configuration
│   └── nginx/nginx.conf      # Nginx configuration
├── docker-compose.yml        # Docker compose configuration
├── Dockerfile                # Main Docker configuration
├── start.sh                 # Quick start script
├── .gitignore               # Git ignore rules
└── README.md               # This file
```

## 🛠️ Development

### Running in Development Mode

1. **Backend Development**
   ```bash
   # Access Laravel container
   docker exec -it pos-laravel-app bash
   
   # Run artisan commands
   php artisan migrate
   php artisan make:controller ApiController
   ```

2. **Frontend Development**
   ```bash
   # The frontend runs with hot-reload enabled
   # Changes are automatically reflected
   ```

### Environment Variables

Backend environment variables are in `backend/.env`:
- Database connection settings
- Application key
- Debug mode settings

## 🏪 Branch-Specific Inventory System

### How It Works
The POS system implements a sophisticated branch-specific inventory management system:

1. **Global Items**: Items are created globally with basic information (name, price, category)
2. **Branch Inventory**: Each branch maintains its own stock levels for each item
3. **Independent Stock**: Branches have separate stock quantities and minimum levels
4. **Transaction Validation**: Only items with available stock at the selected branch can be sold
5. **Automatic Deduction**: Stock decreases automatically when transactions are completed

### Key Benefits
- **Accurate Stock Tracking**: Real-time inventory per location
- **Prevents Overselling**: System validates stock before allowing transactions
- **Branch Autonomy**: Each location manages its own inventory levels
- **Low Stock Alerts**: Automatic alerts when items fall below minimum levels
- **Business Intelligence**: Inventory insights for better decision making

### Database Structure
- `items` table: Global item definitions
- `branch_items` table: Branch-specific stock levels and availability
- Automatic stock deduction through transaction processing
- Low stock alerts and inventory monitoring

## 🚦 Service Charge Calculation

The system automatically applies a 5% service charge to all transactions:
- Subtotal = Sum of all item prices × quantities
- Service Charge = Subtotal × 5%
- Total = Subtotal + Service Charge

## 📊 Reporting Features

### PDF Export
- Filtered transaction history can be exported as PDF
- Includes transaction details, totals, and summaries
- Formatted for professional reporting

### Dashboard Analytics
- Real-time revenue tracking with date filtering
- Low stock alerts and inventory monitoring
- Branch inventory status overview
- Top-selling items analysis based on actual transactions
- Branch performance comparison and revenue tracking
- Visual indicators for inventory management

## 🐛 Troubleshooting

### Common Issues

1. **Docker containers not starting**
   - Ensure Docker is running
   - Check if ports 3000, 8080, 3306 are available
   - Run `docker-compose down` and then `docker-compose up -d`

2. **Database connection errors**
   - Wait for MySQL container to fully initialize
   - Check database credentials in `.env` file
   - Restart the application containers

3. **Frontend not loading**
   - Check if port 3000 is accessible
   - Look at frontend container logs: `docker logs pos-frontend`
   - Ensure backend API is running on port 8080

4. **Permission errors**
   - Run: `docker exec pos-laravel-app chmod -R 777 storage bootstrap/cache`

### Logs
- **Laravel logs**: `docker logs pos-laravel-app`
- **Frontend logs**: `docker logs pos-frontend`
- **Database logs**: `docker logs pos-mysql`
- **Nginx logs**: `docker logs pos-nginx`

## 🔒 Security Considerations

- Change default database passwords in production
- Set appropriate environment variables
- Use HTTPS in production
- Implement proper user authentication (not included in this version)
- Regularly update dependencies

## 🚀 Deployment

For production deployment:

1. Update environment variables
2. Use production-grade database
3. Configure SSL certificates
4. Set up proper backup systems
5. Monitor application performance

## 📝 License

This project is built for educational and demonstration purposes.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📞 Support

For issues and questions:
- Check the troubleshooting section above
- Review Docker logs for error details
- Ensure all prerequisites are met

---

**Built with ❤️ using Laravel and Vue.js**