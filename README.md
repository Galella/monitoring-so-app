# Monitoring SO App

A Laravel-based application for monitoring and reconciling operational data (CM) with financial data (Coin). Ideally suited for logistics operation tracking.

## Core Features

### 1. Dashboard

-   **Key Statistics**: fast view of matched vs missing records.
-   **Volume Charts**:
    -   **Volume per Origin Station**: Grouped Bar Chart showing container volume by Train (`kereta`) per Month.
    -   Responsive and interactive visualizations using **Chart.js**.

### 2. Monitoring & Reconciliation

-   **Automated Matching**: Automatically matches records between `cms` (Operational) and `coins` (Financial/SO) based on Container Number and CM ID.
-   **Status Tracking**:
    -   **Matched**: Valid records present in both systems.
    -   **Missing Coin**: Operational data exists but no corresponding SO/Financial record.
    -   **Missing CM**: Financial record exists but no corresponding Operational data.
-   **smart Filtering**:
    -   **Role-Based Views**: "Admin Area" users automatically see "Missing CM" records relevant to their station (Stasiun Asal), even if the data hasn't been fully mapped to an Area ID yet.
    -   **Search & Export**: Full text search and Excel export capabilities.

### 3. Role-Based Access Control (RBAC)

-   **Super Admin**: Full access to all data and settings.
-   **Admin Area**: Restricted access to data specific to their assigned Area/Station.
-   **Admin Wilayah**: Regional oversight capabilities.

## Technology Stack

-   **Framework**: Laravel 12
-   **UI Theme**: AdminLTE 3 (Bootstrap 4)
-   **Database**: MySQL
-   **Charts**: Chart.js
-   **Excel**: Laravel Excel (Maatwebsite)

## Installation

1. **Clone Repository**
    ```bash
    git clone https://github.com/Galella/monitoring-so-app.git
    ```
2. **Install Dependencies**
    ```bash
    composer install
    npm install
    ```
3. **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4. **Database Migration**
    ```bash
    php artisan migrate --seed
    ```
5. **Run Application**
    ```bash
    php artisan serve
    ```
