# Ticket Reservation System

## Getting Started
#### Note: please make sure you have docker runngin
1. ``` git clone https://github.com/Mosaab4/rs-task```
2. ``cd ts-task``
3. ```sh ./setup.sh```


### Run migrations and seed data
You can use the following command to refresh the database and seed testing data

```./vendor/bin/sail php artisan migrate:fresh --seed```

## Login Credentials
You can use the following credentials to create a token so you can use the APIs:

```
Email:      test@test.com
or          test2@test.com

Password:   password
```

## Reservation flow
The trips endpoint list the basic information about the trip

the trip details endpoint list all the details for the trip including the bus seats and available stations

you can choose stations to select start, end point and IDs for the seats to book the trip

## Tests
To Run all unit tests by running:

``` ./vendor/bin/sail php artisan test```

### Postman Collection
This postman collection contains all the required APIs

[Postman Collection](https://documenter.getpostman.com/view/2179951/2s93RXr9vC)
