# Weather API App

My Design is user-friendly it easy to use, in first glance you may know how to use or navigate it. My code also is well-organized, always ready for the future update, even an junior can understand coz I wrote it in detailed way having a docblock.

## Prerequisites

1.  Ensure you have Docker installed on your machine (for Docker setup). If not, download and install Docker from the [official website](https://www.docker.com/).
    
2.  PHP, Composer, and Laravel CLI should be available on your system (for native engine setup).
    

## Setup

1.  **Clone the Backend Repository**:
`git clone https://github.com/taliffsss/weather-app-api.git
cd weather-app-api` 

2.  **Frontend Repository**:
For the frontend part of the application, refer to: [Weather App Web](https://github.com/taliffsss/weather-app-web.git).



### Running with Docker:

3.1 If you wish to run the application in a Docker container:

`sh docker.sh prod build` 

-   `master` and `main` are used for the production environment.
-   `build` command is used to build the Docker image.
- Now you should be able to access the application on your local host.
Docker setup usage:
Run `sh docker.sh [branch alias] up`
-   **1st parameter (`branch`)**:
    -   `develop` or `dev`: Uses the development environment.
    -   `staging` or `staging`: Uses the staging environment.
    -   `master`, `main`, or `prod`: Uses the production environment.
-   **2nd parameter (`command`)**:
    -   `up`: Start the container.
    -   `build`: Build the Docker container.
    -   `down`: Stop the container.
    -   `ps`: List containers.
    -   `exec`: Execute a command in a running container.

### Running with Native Engine:

3.1 **Install Dependencies**:

`composer install && composer artisan key:generate && php artisan jwt:secret` 

3.2 **Run the Application**:

`php artisan serve` 

The application will start, and by default, it should be available at `http://localhost:8000`.

## API Routes

-   **GET Weather**: `/api/v1/weather?lat=14.5833&lng=120.9667`
-   **GET forecast**: `/api/v1/forecast?lat=14.5833&lng=120.9667`
-   **GET city**: `/api/v1/city?query=manila`

