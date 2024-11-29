# Installation
composer require beproject/monitor

# Usage
The package automatically registers the following endpoints:

GET /beproject/ping?token=123456

GET /beproject/system-health?token=123456

Each request must include a valid token parameter composed of at least 8 consecutive characters from your Laravel APP_KEY
