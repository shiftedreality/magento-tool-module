# NOT FOR PRODUCTION USAGE

# Magento tool integration

This tool provides an API for remote command execution.

## Installation

1. Install as regular Magento 2 module
2. Open your Nginx configuration and replace:

```nginx
    # PHP entry point for main application
    location ~ ^/(index|get|static|errors/report|errors/404|errors/503|health_check)\.php$ {
```

with

```nginx
    # PHP entry point for main application
    location ~ ^/(index|get|static|errors/report|errors/404|errors/503|health_check|remote)\.php$ {
```

3. Copy `dist/remote.php` to `pub` directory
4. Enable module

```
./bin/magento module:enable --all
```

## Authentication

There is no authentication available.

## Usage

1. Retrieve a list of available commands:

```shell
curl -X GET "http://magento2.docker/remote.php"
```

Example response:

```shell
{"admin:user:create":{"description":"Creates an administrator","help":"","usages":[],"definition":[]}}
```

**Note:** The list response for all comands is large

2. Run a specific command

```shell
curl -X GET "http://magento2.docker/remote.php?type=run&name=maintenance:status"
```

Example response:

```shell
Status: maintenance mode is not active
List of exempt IP-addresses: none
```
