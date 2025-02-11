# Project Name
Customer Relation Management System 
## Description
Customer SAtisfaction Feedback

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Running the Project](#running-the-project)
5. [Deployment](#deployment)
6. [Usage](#usage)
10. [Contact Information](#contact-information)

## Prerequisites
- PHP >= 8.0
- Composer
- Node.js >= 14.x

## Installation
1. Clone the repository
    ``bash
    git clone <https://github.com/your-username/your-repo.git> or the github link of this project
    ```

2. Navigate to the project directory
    ```bash
    cd your-repo
    ```

3. Install PHP dependencies
    ```bash
    composer install
    composer update
    ```

4. Install JavaScript dependencies
    ```bash
    npm install
    ```

## Configuration
1. Copy the example environment file and update it with your configuration settings if no .env file
    ```bash
    cp .env.example .env
    ```

2. Generate the application key
    ```bash
    php artisan key:generate
    ```

3. Set up your database credentials in the `.env` file

## Running the Project
1. Run database migrations
    ```bash
    php artisan migrate
    ```

2. Compile assets
    ```bash
    npm run dev
    ```

4. Access the project in your browser
    ```bash
    open http://localhost or <your-domain-name>
    ```

## Deployment
### Deploying to Production
1. Set the environment to production in `.env`
    ```dotenv
    APP_ENV=production
    ```

2. Compile assets for production
    ```bash
    npm run production
    ```

3. Optimize the application
    ```bash
    php artisan optimize
    ```
4. Run database seeders
    ```bash
    php artisan db:seed
    ```

5. Deploy your project using your preferred method

## Usage
- Access the website at `http://localhost` or <your-domain-name> for local development.


## Contact Information
For any inquiries, please contact DOST IX assigned personnel.




