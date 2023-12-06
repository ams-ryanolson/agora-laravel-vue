This code snippet demonstrates the usage of Laravel Jetstream, VueJS, InertiaJS, Agora RTM/RTC/Media Gateway.
It showcases the integration of these technologies to build a real-time communication application.

@package agora-laravel-vue
@version 1.0.0
@author Ryan Olson
@link https://github.com/ams-ryanolson/agora-laravel-vue

## Laravel Jetstream

Laravel Jetstream is a beautifully designed application scaffolding for Laravel. It provides a starting point for your application by including authentication, registration, and other common features.
Learn more: https://jetstream.laravel.com

## VueJS

VueJS is a progressive JavaScript framework for building user interfaces. It allows you to build interactive web applications with ease.
Learn more: https://vuejs.org

## InertiaJS

InertiaJS is a library that allows you to build single-page applications using server-side routing and controllers. It combines the best of both worlds by providing the benefits of a traditional server-rendered application with the responsiveness and interactivity of a modern JavaScript framework.
Learn more: https://inertiajs.com

## Agora RTM/RTC/Media Gateway

Agora is a real-time engagement platform that provides Real-Time Messaging (RTM), Real-Time Communication (RTC), and Media Gateway services. It enables developers to easily integrate real-time communication features into their applications.
Learn more: https://www.agora.io

## Installation Steps

1. Clone the repository: git clone https://github.com/ams-ryanolson/agora-laravel-vue.git
2. Navigate to the project directory: cd agora-laravel-vue
3. Install the dependencies: composer install && npm install
4. Create a copy of the .env.example file and rename it to .env: cp .env.example .env
5. Generate an application key: php artisan key:generate
6. Configure your database settings in the .env file
7. Run the database migrations: php artisan migrate
8. Start the development server: php artisan serve
9. Visit http://localhost:8000 in your browser to see the application

## Project Requirements

The following is what is required for this to be complete for live

/_ This list is currently incomplete _/

1. User is able to start a stream
2. User is able to join a stream
3. HOST is able to invite someone to stage
4. HOST is able to kick someone off stage
5. HOST is able to kick someone from audience
6. HOST is able to remove a message in chat
7. User is able to select if the stream will be audio only on creation

## Notes

We are trying to replicate Instagram Live with Twitter Spaces. The issue with Twitter spaces is they do not allow group chat which is important for engagement. Instagram Live is not desktop friendly.

Both options also do not allow for the engagement of tips and other services which is something we are trying to put in place for long term engagement.

If you are from Agora, please reach out to talk to me more about this. I am happy to give more details.
